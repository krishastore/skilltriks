<?php
/**
 * The file that defines the forgot password shortcode functionality.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Shortcode
 */

namespace ST\Lms\Shortcode;

use ST\Lms\ErrorLog as EL;

/**
 * Shortcode register manage class.
 */
class ForgotPassword extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'forgot_password' );
		$this->init();
		add_filter( 'lostpassword_url', array( $this, 'update_lostpassword_url' ), 10, 2 );
		add_filter( 'retrieve_password_message', array( $this, 'custom_retrieve_password_message' ), 10, 4 );
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
		ob_start();
		load_template( \ST\Lms\locate_template( 'forgot-password.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Update the lost password URL to point to the custom forgot password page.
	 *
	 * @param string $lostpassword_url The default lost password URL.
	 * @param string $redirect         Optional redirect URL after password reset.
	 * @return string Modified lost password URL.
	 */
	public function update_lostpassword_url( $lostpassword_url, $redirect ) {
		$custom_url = home_url( '/stlms-forgot-password' );
		$args       = array(
			'action'      => 'lostpassword',
			'redirect_to' => $redirect ? $redirect : home_url( '/stlms-login/' ),
		);
		return add_query_arg( $args, $custom_url );
	}

	/**
	 * Customize the password reset email message.
	 *
	 * @param string $message    Default message text.
	 * @param string $key        Password reset key.
	 * @param string $user_login User login name.
	 * @param object $user_data  WP_User object.
	 * @return string Modified email message with custom reset link.
	 */
	public function custom_retrieve_password_message( $message, $key, $user_login, $user_data ) {
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$locale    = get_user_locale( $user_data );

		$message = __( 'Someone has requested a password reset for the following account:' ) . "\r\n\r\n";

		/* translators: %s: Site name. */
		$message .= sprintf( __( 'Site Name: %s', 'text-domain' ), $site_name ) . "\r\n\r\n";

		/* translators: %s: Username. */
		$message .= sprintf( __( 'Username: %s', 'text-domain' ), $user_login ) . "\r\n\r\n";

		$message .= __( 'If this was a mistake, ignore this email and nothing will happen.', 'text-domain' ) . "\r\n\r\n";
		$message .= __( 'To reset your password, visit the following address:', 'text-domain' ) . "\r\n\r\n";

		$message .= network_site_url(
			'stlms-forgot-password/?login=' . rawurlencode( $user_login ) . "&key=$key&action=rp",
			'login'
		) . '&wp_lang=' . $locale . "\r\n\r\n";

		return $message;
	}
}
