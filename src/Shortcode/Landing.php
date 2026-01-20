<?php
/**
 * The file that defines the my landing page shortcode functionality.
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
class Landing extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'landing' );
		$this->init();
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );

		$template_name = 'landing-disabled.php';

		if ( class_exists( '\LSI\License\LicenseManager' ) ) :
			if ( \LSI\License\LicenseManager::instance()->is_pro() ) :
				$template_name = 'landing.php';
				wp_enqueue_style( $this->handler . '-swiper' );
				wp_enqueue_script( $this->handler . '-swiper' );
				wp_enqueue_script( $this->handler . '-chart' );
				wp_enqueue_script( $this->handler . '-dashboard' );
			endif;
		endif;
		ob_start();
		load_template( \ST\Lms\locate_template( $template_name ), false, array() );
		$content = ob_get_clean();
		return $content;
	}
}
