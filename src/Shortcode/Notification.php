<?php
/**
 * The file that defines the notifications page shortcode functionality.
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
class Notification extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'notifications' );
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
		$args = shortcode_atts(
			array(
				'filter'     => 'yes',
				'pagination' => 'yes',
			),
			$atts,
			$this->shortcode_tag
		);
		ob_start();
		load_template( \ST\Lms\locate_template( 'notifications.php' ), false, $args );
		$content = ob_get_clean();
		return $content;
	}
}
