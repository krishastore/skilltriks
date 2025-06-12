<?php
/**
 * Notification class for updated assigned course.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Notification;

/**
 * UpdateCourseNotification class.
 */
class UpdateCourseNotification extends \ST\Lms\Helpers\Notification {

	/**
	 * The main instance var.
	 *
	 * @var UpdateCourseNotification|null $instance The one UpdateCourseNotification instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return UpdateCourseNotification Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new UpdateCourseNotification();
		}
		return self::$instance;
	}

	/**
	 * Get subject line for the email notification.
	 *
	 * @param string|null $course_name Course name related to the notification.
	 * @param bool        $is_assigner Optional course assigner.
	 *
	 * @return string
	 */
	public function email_subject( $course_name, $is_assigner = false ) {
		$this->subject = __( 'Updated Due Date for Your Course: ', 'skilltriks' ) . $course_name;

		/**
		 * Filter the course assigned email subject.
		 *
		 * @param string $subject Email subject.
		 */
		return apply_filters( 'stlms/update_course_notification/subject', $this->subject );
	}

	/**
	 * Get body/content of the email notification.
	 *
	 * @param string      $from_user_name User who initiated the action.
	 * @param string      $to_user_name Recipient user.
	 * @param int         $course_id course ID.
	 * @param string|null $due_date Optional course due date.
	 * @param bool        $is_assigner Optional course assigner.
	 *
	 * @return string
	 */
	public function email_message( $from_user_name, $to_user_name, $course_id, $due_date, $is_assigner = false ) {

		$course      = get_post( $course_id );
		$course_name = ! empty( $course ) ? $course->post_title : '';
		$course_link = get_permalink( $course_id );

		$this->message = $this->render_email_template(
			'update-course-email-template',
			array(
				'from_user'   => $from_user_name,
				'to_user'     => $to_user_name,
				'course_name' => $course_name,
				'due_date'    => $due_date,
				'course_link' => $course_link,
			)
		);

		/**
		 * Filter the course assigned email body.
		 *
		 * @param string $message Email message template.
		 */
		return apply_filters( 'stlms/update_course_notification/message', $this->message );
	}

	/**
	 * Check if this notification should send an email.
	 *
	 * @return bool
	 */
	public function should_send_email_notification() {
		/**
		 * Filter to enable/disable sending course assigned emails.
		 *
		 * @param bool $should_send_email Default true.
		 */
		return apply_filters( 'stlms/update_course_notification/should_send_email', true );
	}
}
