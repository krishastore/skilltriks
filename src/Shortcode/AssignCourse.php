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
use const ST\Lms\STLMS_COURSE_ASSIGN_BY_ME;
use const ST\Lms\STLMS_COURSE_ASSIGN_TO_ME;
use const ST\Lms\META_KEY_COURSE_ASSIGNED;

/**
 * Shortcode register manage class.
 */
class AssignCourse extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'assign_course_by_me' );
		add_action( 'wp_ajax_update_assign_course', array( $this, 'update_assign_course' ) );
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

	/**
	 * Edit and delete the assigned course by me.
	 */
	public function update_assign_course() {
		check_ajax_referer( STLMS_BASEFILE, '_nonce' );

		if ( ! isset( $_POST['type'], $_POST['key'] ) || empty( $_POST['type'] ) || empty( $_POST['key'] ) ) {
			return;
		}

		$type                   = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		$key                    = sanitize_text_field( wp_unslash( $_POST['key'] ) );
		$course_id              = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$completion_date        = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
		list( $cid, $_user_id ) = explode( '_', $key, 2 );
		$cid                    = (int) $cid;
		$_user_id               = (int) $_user_id;
		$curr_user_id           = get_current_user_id();

		$course_assigned_by_me = get_user_meta( $curr_user_id, STLMS_COURSE_ASSIGN_BY_ME, true ) ? get_user_meta( $curr_user_id, STLMS_COURSE_ASSIGN_BY_ME, true ) : array();
		$course_assigned_to_me = get_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, true ) ? get_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, true ) : array();
		$existing_users        = get_post_meta( $cid, META_KEY_COURSE_ASSIGNED, true ) ? get_post_meta( $cid, META_KEY_COURSE_ASSIGNED, true ) : array();

		if ( 'delete' === $type || ( 'edit' === $type && $cid !== $course_id ) ) {
			if ( array_key_exists( $key, $course_assigned_by_me ) ) {
				unset( $course_assigned_by_me[ $key ] );
				update_user_meta( $curr_user_id, STLMS_COURSE_ASSIGN_BY_ME, $course_assigned_by_me );
			}
			if ( array_key_exists( "{$cid}_{$curr_user_id}", $course_assigned_to_me ) ) {
				unset( $course_assigned_to_me[ "{$cid}_{$curr_user_id}" ] );
				update_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, $course_assigned_to_me );
			}
			if ( in_array( (int) $_user_id, $existing_users, true ) ) {
				$existing_users = array_diff( $existing_users, array( (int) $_user_id ) );
				update_post_meta( $cid, META_KEY_COURSE_ASSIGNED, $existing_users );
			}
		}

		if ( 'edit' === $type ) {
			$course_assigned_by_me = get_user_meta( $curr_user_id, STLMS_COURSE_ASSIGN_BY_ME, true ) ? get_user_meta( $curr_user_id, STLMS_COURSE_ASSIGN_BY_ME, true ) : array();
			$course_assigned_to_me = get_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, true ) ? get_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, true ) : array();
			$existing_users        = get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true ) ? get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true ) : array();

			$course_assigned_by_me[ "{$course_id}_{$_user_id}" ]     = $completion_date;
			$course_assigned_to_me[ "{$course_id}_{$curr_user_id}" ] = $completion_date;

			if ( ! in_array( (int) $_user_id, $existing_users, true ) ) {
				$existing_users[] = (int) $_user_id;
			}

			update_user_meta( $curr_user_id, STLMS_COURSE_ASSIGN_BY_ME, $course_assigned_by_me );
			update_user_meta( $_user_id, STLMS_COURSE_ASSIGN_TO_ME, $course_assigned_to_me );
			update_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, $existing_users );
		}

		wp_send_json_success( array( 'message' => 'Updated successfully.' ) );
	}
}
