<?php
/**
 * The file that defines the assign course by me shortcode functionality.
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
class AssignCourse extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'assign_course_by_me' );
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
		wp_enqueue_style( $this->handler . '-assigncourse' );
		wp_enqueue_script( $this->handler . '-assigncourse' );
		ob_start();
		load_template( \ST\Lms\locate_template( 'assign-course-by-me.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}
}
