<?php
/**
 * The file that defines the assign new course shortcode functionality.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Shortcode
 *
 * phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
 */

namespace ST\Lms\Shortcode;

use ST\Lms\ErrorLog as EL;
use const ST\Lms\STLMS_COURSE_ASSIGN_BY_ME;
use const ST\Lms\STLMS_COURSE_ASSIGN_TO_ME;
use const ST\Lms\META_KEY_COURSE_ASSIGNED;

/**
 * Shortcode register manage class.
 */
class AssignNewCourse extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'assign_new_course' );
		add_action( 'wp_ajax_assign_new_course', array( $this, 'assign_course' ) );
		add_action( 'wp_ajax_get_assigned_users', array( $this, 'get_assigned_users' ) );
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
		load_template( \ST\Lms\locate_template( 'assign-new-course.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Assign new course
	 */
	public function assign_course() {
		check_ajax_referer( STLMS_BASEFILE, '_nonce' );
		if ( isset( $_POST['assign_course_data'] ) && is_array( $_POST['assign_course_data'] ) ) {
			$assign_course_data = $_POST['assign_course_data'];
			$current_user_id    = get_current_user_id();
			$users              = array();
			$course_id          = 0;

			$course_assigned_by_me = get_user_meta( $current_user_id, STLMS_COURSE_ASSIGN_BY_ME, true );
			if ( ! is_array( $course_assigned_by_me ) ) {
				$course_assigned_by_me = array();
			}

			foreach ( $assign_course_data as $assignment ) {
				$course_id       = isset( $assignment['course_id'] ) ? (int) $assignment['course_id'] : 0;
				$_user_id        = isset( $assignment['user_id'] ) ? (int) $assignment['user_id'] : 0;
				$completion_date = isset( $assignment['completion_date'] ) ? sanitize_text_field( $assignment['completion_date'] ) : '';

				if ( empty( $course_id ) || empty( $_user_id ) ) {
					continue;
				}

				// Assigner’s key: course_id + user_id.
				$assigner_key                           = "{$course_id}_{$_user_id}";
				$course_assigned_by_me[ $assigner_key ] = $completion_date;
				$assignee_key                           = "{$course_id}_{$current_user_id}";
				$assigned_to_me                         = get_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, true );
				if ( ! is_array( $assigned_to_me ) ) {
					$assigned_to_me = array();
				}
				$assigned_to_me[ $assignee_key ] = $completion_date;
				$users[]                         = $_user_id;

				update_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, $assigned_to_me );
			}

			$existing_users = get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true ) ? get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true ) : array();
			$merged_users   = array_unique( array_merge( $existing_users, $users ) );

			// Update current user’s assigned_by_me meta after loop.
			update_user_meta( $current_user_id, STLMS_COURSE_ASSIGN_BY_ME, $course_assigned_by_me );
			// Update course meta key to avoid assigning course to same user.
			update_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, $merged_users );
		}
	}

	/**
	 * Get the list of already assigned users to the course.
	 */
	public function get_assigned_users() {
		check_ajax_referer( STLMS_BASEFILE, '_nonce' );

		$course_id = isset( $_POST['course_id'] ) ? (int) $_POST['course_id'] : 0;
		if ( ! $course_id ) {
			wp_send_json_error( 'Invalid course ID.' );
		}

		$assigned_users = get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true );
		if ( ! is_array( $assigned_users ) ) {
			$assigned_users = array();
		}

		wp_send_json_success( $assigned_users );
	}
}
