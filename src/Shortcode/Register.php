<?php
/**
 * The file that defines the shortcode register functionality.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Shortcode
 */

namespace ST\Lms\Shortcode;

use const ST\Lms\STLMS_SCRIPT_HANDLE;
use const ST\Lms\STLMS_QUESTION_VALIDATE_NONCE;

/**
 * Shortcode register manage class.
 */
abstract class Register {

	/**
	 * Shortcode tagName.
	 *
	 * @var string $shortcode_tag
	 * @since 1.0.0
	 */
	public $shortcode_tag = '';

	/**
	 * Script/Style handler.
	 *
	 * @var string $handler Handler.
	 */
	public $handler = STLMS_SCRIPT_HANDLE . 'frontend';

	/**
	 * Init hooks.
	 */
	public function init() {
		// Calling hooks.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		if ( ! shortcode_exists( $this->shortcode_tag ) ) {
			add_shortcode( 'stlms_' . $this->shortcode_tag, array( $this, 'register_shortcode' ) );
		}
	}

	/**
	 * Set shortcode tab.
	 *
	 * @param string $tag Shortcode tag name.
	 */
	public function set_shortcode_tag( $tag = '' ) {
		$this->shortcode_tag = $tag;
	}

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts() {
		$version = stlms_run()->get_version();
		if ( defined( 'STLMS_ASSETS_VERSION' ) && ! empty( STLMS_ASSETS_VERSION ) ) {
			$version = STLMS_ASSETS_VERSION;
		}
		if ( function_exists( 'stlms_addons_styles' ) ) {
			\stlms_addons_styles();
		} else {
			wp_register_style( $this->handler, STLMS_ASSETS . '/css/frontend.css', array(), $version );
		}
		wp_register_script( $this->handler, STLMS_ASSETS . '/js/build/frontend.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-plyr', STLMS_ASSETS . '/js/build/plyr.js', array( 'jquery', $this->handler ), $version, true );
		wp_register_script( $this->handler . '-smartwizard', STLMS_ASSETS . '/js/build/smartwizard.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-countdowntimer', STLMS_ASSETS . '/js/build/countdowntimer.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-swiper', STLMS_ASSETS . '/js/build/swiper.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-assigncourse', STLMS_ASSETS . '/js/build/assigncourse.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-userprofile', STLMS_ASSETS . '/js/build/userprofile.js', array( 'jquery' ), $version, true );

		$curriculum_type = get_query_var( 'curriculum_type' );
		$userinfo        = wp_get_current_user();
		$user_name       = $userinfo->display_name;

		wp_localize_script(
			$this->handler,
			'StlmsObject',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'securityNonce'   => wp_create_nonce( STLMS_QUESTION_VALIDATE_NONCE ),
				'nonce'           => wp_create_nonce( STLMS_BASEFILE ),
				'quizId'          => ! empty( $curriculum_type ) && 'quiz' === $curriculum_type ? (int) get_query_var( 'item_id' ) : 0,
				'courseId'        => ! empty( $curriculum_type ) && 'quiz' === $curriculum_type ? get_the_ID() : 0,
				'fileName'        => 'ST-' . substr( strtoupper( wp_hash( $user_name ) ), 0, 5 ),
				'currentUrl'      => get_the_permalink(),
				'iconUrl'         => STLMS_ASSETS . '/images/plyr.svg',
				'blankVideo'      => STLMS_ASSETS . '/images/blank.mp4',
				'assignCourseUrl' => \ST\Lms\get_page_url( 'assign_course_by_me' ),
			)
		);

		wp_register_style( $this->handler . '-plyr', STLMS_ASSETS . '/css/plyr.css', array(), $version );
		wp_register_style( $this->handler . '-smartwizard', STLMS_ASSETS . '/css/smartwizard.css', array(), $version );
		wp_register_style( $this->handler . '-swiper', STLMS_ASSETS . '/css/swiper.css', array(), $version );
		wp_register_style( $this->handler . '-assigncourse', STLMS_ASSETS . '/css/assigncourse.css', array(), $version );
		wp_register_style( $this->handler . '-userprofile', STLMS_ASSETS . '/css/userprofile.css', array(), $version );

		wp_localize_script(
			$this->handler . '-userprofile',
			'StlmsRestObj',
			array(
				'restMediaUrl' => esc_url_raw( rest_url( 'wp/v2/media' ) ),
				'restUserUrl'  => esc_url_raw( rest_url( 'wp/v2/users/me' ) ),
				'nonce'        => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {}
}
