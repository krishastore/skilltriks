<?php
/**
 * The file that defines the user profile shortcode functionality.
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
class UserProfile extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'user_profile' );
		add_action( 'init', array( $this, 'skilltriks_register_user_meta' ) );
		$this->init();
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_script( $this->handler . '-userprofile' );
		wp_enqueue_style( $this->handler . '-userprofile' );
		// Core scripts needed for password generator + strength meter.
		wp_enqueue_script( 'user-profile' );
		wp_enqueue_script( 'password-strength-meter' );
		wp_enqueue_script( 'zxcvbn-async' );

		ob_start();
		load_template( \ST\Lms\locate_template( 'userprofile.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Register user meta to store the user profile pic.
	 */
	public function skilltriks_register_user_meta() {
		register_meta(
			'user',
			'avatar_url',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => function ( $allowed, $meta_key, $user_id ) {
					return get_current_user_id() === (int) $user_id;
				},
			)
		);
	}
}
