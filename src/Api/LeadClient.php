<?php
/**
 * Outbound client for the SkillTriks lead endpoint.
 *
 * Posts an HMAC-signed payload to skilltriks.com when a site owner opts in
 * through the subscribe notice. Every call is best-effort: the local option
 * written by SubscribeNotice is the record of truth, and a failure here must
 * never surface to the user.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.3.0
 *
 * @package    ST\Lms\Api
 */

namespace ST\Lms\Api;

use ST\Lms\ErrorLog as EL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * LeadClient Class.
 */
class LeadClient {

	/**
	 * Default production endpoint.
	 */
	const DEFAULT_ENDPOINT = 'https://www.skilltriks.com/wp-json/skilltriks/v1/leads';

	/**
	 * Signing key id this build ships with.
	 *
	 * The receiver keeps a map of key id => secret and must accept this id
	 * for as long as any site still runs this plugin version, which in
	 * practice means forever. Rotating means shipping a new id alongside the
	 * old one, never replacing it.
	 */
	const DEFAULT_KEY_ID = 'v1';

	/**
	 * Shared signing secret this build ships with.
	 *
	 * This is not a secret in any meaningful sense: the plugin is distributed
	 * through wordpress.org, so anyone can unzip it and read this value. It
	 * raises the bar against bots scanning /wp-json and proves the payload was
	 * not altered in transit - nothing more. Abuse protection on the receiving
	 * end is rate limiting, not this string.
	 */
	const DEFAULT_SECRET = '691bde1395bbfabab6fd9f7625b5d7b71715d09d453f595b5fcb860b4c9d265d';

	/**
	 * Option holding this install's stable identifier.
	 */
	const SITE_HASH_OPTION = 'stlms_site_hash';

	/**
	 * Option holding the outcome of the last sync attempt.
	 */
	const SYNC_OPTION = 'stlms_lead_sync';

	/**
	 * Request timeout in seconds.
	 *
	 * Deliberately short: this call happens inside the AJAX request that the
	 * notice is waiting on, so a hung endpoint must not stall the UI.
	 */
	const TIMEOUT = 5;

	/**
	 * Endpoint URL.
	 *
	 * @return string
	 */
	public static function endpoint() {
		$endpoint = defined( 'STLMS_LEAD_ENDPOINT' ) ? STLMS_LEAD_ENDPOINT : self::DEFAULT_ENDPOINT;

		/**
		 * Filter the lead endpoint URL.
		 *
		 * @param string $endpoint Endpoint URL.
		 */
		return (string) apply_filters( 'stlms/lead/endpoint', $endpoint );
	}

	/**
	 * Signing key id.
	 *
	 * @return string
	 */
	public static function key_id() {
		$key_id = defined( 'STLMS_LEAD_KEY_ID' ) ? STLMS_LEAD_KEY_ID : self::DEFAULT_KEY_ID;

		/**
		 * Filter the signing key id.
		 *
		 * @param string $key_id Key id.
		 */
		return (string) apply_filters( 'stlms/lead/key_id', $key_id );
	}

	/**
	 * Shared signing secret.
	 *
	 * @return string
	 */
	public static function secret() {
		$secret = defined( 'STLMS_LEAD_SECRET' ) ? STLMS_LEAD_SECRET : self::DEFAULT_SECRET;

		/**
		 * Filter the signing secret.
		 *
		 * @param string $secret Shared secret.
		 */
		return (string) apply_filters( 'stlms/lead/secret', $secret );
	}

	/**
	 * Whether syncing is configured and switched on.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$enabled = self::endpoint() && self::key_id() && self::secret();

		/**
		 * Filter whether leads are sent at all.
		 *
		 * @param bool $enabled Enabled state.
		 */
		return (bool) apply_filters( 'stlms/lead/enabled', $enabled );
	}

	/**
	 * Stable identifier for this install.
	 *
	 * Generated once and reused, so the receiver can still recognise a site
	 * after its domain changes.
	 *
	 * @return string
	 */
	public static function site_hash() {
		$hash = get_option( self::SITE_HASH_OPTION );

		if ( ! $hash ) {
			$hash = wp_generate_uuid4();
			update_option( self::SITE_HASH_OPTION, $hash, false );
		}

		return (string) $hash;
	}

	/**
	 * Whether the endpoint host is a local development host.
	 *
	 * @param string $url Endpoint URL.
	 * @return bool
	 */
	private static function is_local_host( $url ) {
		$host = wp_parse_url( $url, PHP_URL_HOST );

		if ( ! $host ) {
			return false;
		}

		if ( in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
			return true;
		}

		return (bool) preg_match( '/\.(test|local|localhost|invalid|example)$/i', $host );
	}

	/**
	 * Build the payload sent to the receiver.
	 *
	 * @param string $email  Subscriber email.
	 * @param string $source Where the submission came from.
	 * @return array
	 */
	private static function build_payload( $email, $source ) {
		global $wp_version;

		return array(
			'email'          => $email,
			'site_url'       => home_url(),
			'site_hash'      => self::site_hash(),
			'site_name'      => get_bloginfo( 'name' ),
			'plugin_version' => defined( 'STLMS_VERSION' ) ? STLMS_VERSION : '',
			'wp_version'     => $wp_version,
			'php_version'    => PHP_VERSION,
			'locale'         => get_locale(),
			'source'         => $source,
			// The user submitting the form is the consent. The Cancel path
			// never reaches this class.
			'consent'        => true,
			'timestamp'      => time(),
		);
	}

	/**
	 * Send a lead.
	 *
	 * @param string $email  Subscriber email.
	 * @param string $source Submission source.
	 * @return array{ok:bool,code:string,http:int,retryable:bool,message:string}
	 */
	public static function send( $email, $source = 'activation_popup' ) {
		if ( ! is_email( $email ) ) {
			return self::result( false, 'invalid_email', 0, false, 'Invalid email address.' );
		}

		if ( ! self::is_enabled() ) {
			return self::result( false, 'disabled', 0, false, 'Lead sync is not configured.' );
		}

		$endpoint  = self::endpoint();
		$timestamp = (string) time();

		// Encode once. These exact bytes are both signed and sent - never
		// re-encode, or the server recomputes a different signature.
		$body = wp_json_encode( self::build_payload( $email, $source ) );

		if ( false === $body ) {
			return self::result( false, 'encode_failed', 0, false, 'Could not encode the payload.' );
		}

		$signature = hash_hmac( 'sha256', $timestamp . "\n" . $body, self::secret() );

		$sslverify = true;
		if ( defined( 'STLMS_LEAD_INSECURE_LOCAL' ) && STLMS_LEAD_INSECURE_LOCAL && self::is_local_host( $endpoint ) ) {
			// Only ever relaxed for local hosts, never for a public endpoint.
			$sslverify = false;
		}

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout'     => self::TIMEOUT,
				'blocking'    => true,
				'sslverify'   => $sslverify,
				'redirection' => 0,
				'user-agent'  => sprintf(
					'SkillTriks/%s; %s',
					defined( 'STLMS_VERSION' ) ? STLMS_VERSION : '0',
					home_url()
				),
				'headers'     => array(
					'Content-Type'   => 'application/json',
					'Accept'         => 'application/json',
					'X-ST-Key-Id'    => self::key_id(),
					'X-ST-Timestamp' => $timestamp,
					'X-ST-Signature' => $signature,
				),
				'body'        => $body,
			)
		);

		// Network level failure: DNS, refused connection, timeout.
		if ( is_wp_error( $response ) ) {
			return self::result( false, 'http_error', 0, true, $response->get_error_message() );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		$parsed = json_decode( wp_remote_retrieve_body( $response ), true );
		$code   = is_array( $parsed ) && isset( $parsed['code'] ) ? (string) $parsed['code'] : '';

		// 200 existing, 201 created, 409 already processed - all terminal successes.
		if ( in_array( $status, array( 200, 201, 409 ), true ) ) {
			return self::result( true, $code ? $code : 'ok', $status, false, '' );
		}

		// Throttled or server-side fault: worth one retry.
		if ( 429 === $status || $status >= 500 ) {
			return self::result( false, $code ? $code : 'server_error', $status, true, 'Endpoint returned ' . $status );
		}

		// 400/401/403 and friends will fail identically next time.
		return self::result( false, $code ? $code : 'rejected', $status, false, 'Endpoint returned ' . $status );
	}

	/**
	 * Normalise a result array.
	 *
	 * @param bool   $ok        Success flag.
	 * @param string $code      Machine-readable code.
	 * @param int    $http      HTTP status.
	 * @param bool   $retryable Whether retrying could succeed.
	 * @param string $message   Human-readable detail.
	 * @return array{ok:bool,code:string,http:int,retryable:bool,message:string}
	 */
	private static function result( $ok, $code, $http, $retryable, $message ) {
		return array(
			'ok'        => (bool) $ok,
			'code'      => (string) $code,
			'http'      => (int) $http,
			'retryable' => (bool) $retryable,
			'message'   => (string) $message,
		);
	}

	/**
	 * Persist the outcome so support can see why a lead never arrived.
	 *
	 * @param array $result   Result from send().
	 * @param int   $attempts Attempt counter.
	 * @return void
	 */
	public static function record_result( array $result, $attempts = 1 ) {
		update_option(
			self::SYNC_OPTION,
			array(
				'ok'       => $result['ok'],
				'code'     => $result['code'],
				'http'     => $result['http'],
				'message'  => $result['message'],
				'attempts' => (int) $attempts,
				'time'     => current_time( 'mysql' ),
			),
			false
		);

		if ( ! $result['ok'] ) {
			EL::add(
				sprintf(
					'Lead sync failed (%s, HTTP %d): %s',
					$result['code'],
					$result['http'],
					$result['message']
				),
				'error',
				__FILE__,
				__LINE__
			);
		}
	}
}
