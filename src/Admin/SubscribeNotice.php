<?php
/**
 * Subscribe notice handler.
 *
 * Renders a notice on every SkillTriks LMS admin screen asking the site owner
 * to subscribe for updates. Each submit or cancel hides it for that user: one
 * month after the first, six months after the second, and for good after the
 * third.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.3.0
 *
 * @package    ST\Lms\Admin
 */

namespace ST\Lms\Admin;

use ST\Lms\Api\LeadClient;
use ST\Lms\ErrorLog as EL;

use const ST\Lms\PARENT_MENU_SLUG;
use const ST\Lms\STLMS_SUBSCRIBE_NOTICE_STATE;
use const ST\Lms\STLMS_SUBSCRIBER_EMAIL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SubscribeNotice Class.
 */
class SubscribeNotice {

	/**
	 * Responses (submit or cancel) after which the notice never returns.
	 */
	const MAX_RESPONSES = 3;

	/**
	 * Capability required to see and act on the notice.
	 */
	const CAPABILITY = 'activate_plugins';

	/**
	 * Cron hook used to retry a failed lead sync once.
	 */
	const RETRY_HOOK = 'stlms_retry_lead_sync';

	/**
	 * The main instance var.
	 *
	 * @var SubscribeNotice|null $instance The one SubscribeNotice instance.
	 * @since 1.3.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return SubscribeNotice Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new SubscribeNotice();
		}
		return self::$instance;
	}

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'in_admin_header', array( $this, 'render_notice' ) );
		add_action( 'wp_ajax_stlms_submit_subscribe_email', array( $this, 'ajax_submit_email' ) );
		add_action( 'wp_ajax_stlms_dismiss_subscribe_notice', array( $this, 'ajax_dismiss_notice' ) );

		/*
		 * WP-Cron fires on frontend requests too, so this callback must be
		 * registered outside the admin. That holds today because Admin\Core is
		 * instantiated unconditionally in \ST\Lms\Core::init(). If that is ever
		 * moved behind an is_admin() guard, retries stop firing silently.
		 */
		add_action( self::RETRY_HOOK, array( $this, 'retry_lead_sync' ), 10, 2 );
	}

	/**
	 * Whether the current admin screen belongs to SkillTriks LMS.
	 *
	 * Plugin post types, taxonomies and submenu pages all carry the `stlms`
	 * prefix or the parent menu slug in their screen ID.
	 *
	 * @since 1.3.0
	 * @return bool
	 */
	private function is_plugin_screen() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return false;
		}

		$is_plugin_screen = false !== strpos( $screen->id, 'stlms' )
			|| false !== strpos( $screen->id, PARENT_MENU_SLUG )
			|| 0 === strpos( (string) $screen->post_type, 'stlms_' )
			|| 0 === strpos( (string) $screen->taxonomy, 'stlms_' );

		return (bool) apply_filters( 'stlms/subscribe_notice/is_plugin_screen', $is_plugin_screen, $screen );
	}

	/**
	 * Whether the notice should render for the current request.
	 *
	 * @since 1.3.0
	 * @return bool
	 */
	private function should_show_notice() {
		if ( ! is_user_logged_in() || ! current_user_can( self::CAPABILITY ) ) {
			return false;
		}
		$state = self::get_state();
		if ( $state['responses'] >= self::MAX_RESPONSES || time() < $state['next_show'] ) {
			return false;
		}
		return $this->is_plugin_screen();
	}

	/**
	 * Notice schedule for a user.
	 *
	 * @since 1.3.0
	 *
	 * @param int $user_id User ID. Defaults to the current user.
	 * @return array{responses:int,next_show:int}
	 */
	private static function get_state( $user_id = 0 ) {
		$user_id = $user_id ? (int) $user_id : get_current_user_id();
		$state   = get_user_meta( $user_id, STLMS_SUBSCRIBE_NOTICE_STATE, true );
		$state   = is_array( $state ) ? $state : array();

		return array(
			'responses' => isset( $state['responses'] ) ? (int) $state['responses'] : 0,
			'next_show' => isset( $state['next_show'] ) ? (int) $state['next_show'] : 0,
		);
	}

	/**
	 * Record a submit or cancel and push the notice out accordingly.
	 *
	 * First response hides it for a month, the second for six months, and
	 * the third retires it for good.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	private static function record_response() {
		$user_id = get_current_user_id();
		$state   = self::get_state( $user_id );

		++$state['responses'];

		/**
		 * Filter how long the notice stays hidden after each response.
		 *
		 * @param array<int,int> $intervals Response number => seconds hidden.
		 */
		$intervals = (array) apply_filters(
			'stlms/subscribe_notice/intervals',
			array(
				1 => MONTH_IN_SECONDS,
				2 => 6 * MONTH_IN_SECONDS,
			)
		);

		$state['next_show'] = isset( $intervals[ $state['responses'] ] )
			? time() + (int) $intervals[ $state['responses'] ]
			: 0;

		update_user_meta( $user_id, STLMS_SUBSCRIBE_NOTICE_STATE, $state );
	}

	/**
	 * Enqueue scripts and styles when the notice is due.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! $this->should_show_notice() ) {
			return;
		}

		$version = defined( 'STLMS_VERSION' ) ? STLMS_VERSION : false;

		wp_enqueue_style(
			'stlms-subscribe-notice',
			STLMS_ASSETS . '/css/subscribe-notice.css',
			array( 'dashicons' ),
			$version
		);

		wp_enqueue_script(
			'stlms-subscribe-notice',
			STLMS_ASSETS . '/js/build/subscribe-notice.js',
			array( 'jquery' ),
			$version,
			true
		);

		wp_localize_script(
			'stlms-subscribe-notice',
			'stlmsSubscribeObject',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'stlms_subscribe_notice_nonce' ),
				'i18n'    => array(
					'invalidEmail' => __( 'Please enter a valid email address.', 'skilltriks' ),
					'serverError'  => __( 'An error occurred. Please try again.', 'skilltriks' ),
				),
			)
		);
	}

	/**
	 * Render the notice above the admin page content.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function render_notice() {
		if ( ! $this->should_show_notice() ) {
			return;
		}

		$current_user  = wp_get_current_user();
		$default_email = $current_user->user_email ? $current_user->user_email : '';
		?>
		<div id="stlms-subscribe-notice" class="stlms-subscribe-notice" role="region" aria-labelledby="stlms-subscribe-title">
			<div class="stlms-subscribe-form-view">
				<span class="stlms-subscribe-pill"><?php esc_html_e( 'Recommended', 'skilltriks' ); ?></span>
				<p id="stlms-subscribe-title" class="stlms-subscribe-title"><?php esc_html_e( 'Stay Connected with SkillTriks LMS!', 'skilltriks' ); ?></p>
				<p class="stlms-subscribe-description"><?php esc_html_e( 'Enter your email address to receive important SkillTriks product updates, new feature announcements, release notifications, and practical LMS tips. Your email helps us keep you informed about what\'s new.', 'skilltriks' ); ?></p>

				<form id="stlms-subscribe-form" class="stlms-subscribe-form" method="post" action="">
					<div class="stlms-subscribe-field">
						<label for="stlms_subscribe_email"><?php esc_html_e( 'Your work email address', 'skilltriks' ); ?></label>
						<div class="stlms-subscribe-input">
							<span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
							<input type="email" id="stlms_subscribe_email" name="stlms_subscribe_email" placeholder="<?php esc_attr_e( 'you@company.com', 'skilltriks' ); ?>" value="<?php echo esc_attr( $default_email ); ?>" required />
						</div>
					</div>

					<p id="stlms-subscribe-message" class="stlms-subscribe-message" style="display:none;" role="alert" aria-live="polite"></p>

					<div class="stlms-subscribe-actions">
						<button type="submit" id="stlms-subscribe-submit-btn" class="button button-primary stlms-btn-submit">
							<?php esc_html_e( 'Subscribe & Continue', 'skilltriks' ); ?>
						</button>
						<button type="button" id="stlms-subscribe-cancel-btn" class="button stlms-btn-cancel">
							<?php esc_html_e( 'Cancel', 'skilltriks' ); ?>
						</button>
						<span class="spinner"></span>
					</div>
				</form>
			</div>

			<div class="stlms-subscribe-success-view" style="display:none;" role="status" aria-live="polite">
				<div class="stlms-subscribe-success-inner">
					<span class="stlms-subscribe-success-icon" aria-hidden="true">
						<span class="dashicons dashicons-yes"></span>
					</span>
					<div class="stlms-subscribe-success-body">
						<div class="stlms-subscribe-success-heading">
							<p class="stlms-subscribe-success-title"><?php esc_html_e( "You're All Set!", 'skilltriks' ); ?></p>
							<span class="stlms-subscribe-pill"><?php esc_html_e( 'Subscription successful', 'skilltriks' ); ?></span>
						</div>
						<p class="stlms-subscribe-success-text"><?php esc_html_e( "You're now subscribed to SkillTriks updates. We'll keep you informed about new features, product improvements, and LMS best practices.", 'skilltriks' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler for submitting email.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function ajax_submit_email() {
		check_ajax_referer( 'stlms_subscribe_notice_nonce', 'nonce' );

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to perform this action.', 'skilltriks' ) ),
				403
			);
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'skilltriks' ) ) );
		}

		// One subscriber per site: a later submission replaces the earlier one.
		update_option( STLMS_SUBSCRIBER_EMAIL, $email );

		// Send email notification to site admin.
		$admin_email = get_option( 'admin_email' );
		$site_name   = get_bloginfo( 'name' );
		// Translators: %s is the site name.
		$subject = sprintf( __( '[%s] New SkillTriks LMS Subscriber Registration', 'skilltriks' ), $site_name );
		$message = sprintf(
			/* Translators: 1: subscriber email, 2: site URL, 3: submission time. */
			__( "A user subscribed to SkillTriks LMS updates with their email address:\n\nEmail: %1\$s\nSite: %2\$s\nTime: %3\$s", 'skilltriks' ),
			$email,
			home_url(),
			current_time( 'mysql' )
		);

		if ( ! wp_mail( $admin_email, $subject, $message ) ) {
			EL::add(
				sprintf( 'Subscriber notification could not be sent to %s.', $admin_email ),
				'error',
				__FILE__,
				__LINE__
			);
		}

		// Forward to skilltriks.com. Best-effort: the option above already
		// holds the record, so nothing here can fail the user's submission.
		$this->sync_lead( $email );

		self::record_response();

		wp_send_json_success( array( 'message' => __( 'Email submitted successfully.', 'skilltriks' ) ) );
	}

	/**
	 * Push the lead to the remote endpoint and schedule a retry on failure.
	 *
	 * @since 1.3.0
	 *
	 * @param string $email Subscriber email.
	 * @return void
	 */
	private function sync_lead( $email ) {
		$result = LeadClient::send( $email );
		LeadClient::record_result( $result, 1 );

		if ( $result['ok'] || ! $result['retryable'] ) {
			return;
		}

		if ( wp_next_scheduled( self::RETRY_HOOK, array( $email, 2 ) ) ) {
			return;
		}

		wp_schedule_single_event(
			time() + ( 15 * MINUTE_IN_SECONDS ),
			self::RETRY_HOOK,
			array( $email, 2 )
		);
	}

	/**
	 * Cron callback: one retry, then give up.
	 *
	 * Retrying a 400 or 401 forever would just burn cron slots, so only
	 * retryable outcomes get here and none of them reschedule.
	 *
	 * @since 1.3.0
	 *
	 * @param string $email   Subscriber email.
	 * @param int    $attempt Attempt number.
	 * @return void
	 */
	public function retry_lead_sync( $email, $attempt = 2 ) {
		$result = LeadClient::send( $email );
		LeadClient::record_result( $result, (int) $attempt );
	}

	/**
	 * AJAX handler for the Cancel button.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function ajax_dismiss_notice() {
		check_ajax_referer( 'stlms_subscribe_notice_nonce', 'nonce' );

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to perform this action.', 'skilltriks' ) ),
				403
			);
		}

		self::record_response();

		wp_send_json_success();
	}
}
