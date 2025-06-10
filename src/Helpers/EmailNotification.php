<?php
/**
 * Declare the abstract Notification class.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 */

namespace ST\Lms\Helpers;

/**
 * Main Notification class.
 */
abstract class Notification {

	/**
	 * Email Subject.
	 *
	 * @var string $subject
	 */
	public $subject;

	/**
	 * Email message.
	 *
	 * @var string $message
	 */
	public $message;

	/**
	 * Send Email or not.
	 *
	 * @var bool $should_send_email
	 */
	public $should_send_email;

	/**
	 * Get subject line for the email notification.
	 *
	 * @param string|null $course_name Course name related to the notification.
	 *
	 * @return string
	 */
	abstract public function email_subject( $course_name );

	/**
	 * Get body/content of the email notification.
	 *
	 * @param string      $from_user_name User who initiated the action.
	 * @param string      $to_user_name Recipient user.
	 * @param int         $course_id course ID.
	 * @param string|null $due_date Optional course due date.
	 *
	 * @return string
	 */
	abstract public function email_message( $from_user_name, $to_user_name, $course_id, $due_date );

	/**
	 * Return true if this notification should also send an email.
	 *
	 * @return bool
	 */
	abstract public function should_send_email_notification();

	/**
	 * Render an email template with data.
	 *
	 * @param string $template_name name of the template.
	 * @param array  $args          Data to pass to the template.
	 *
	 * @return string Rendered HTML message.
	 */
	protected function render_email_template( $template_name, $args = array() ) {
		ob_start();

		$template_full_path = STLMS_TEMPLATEPATH . '/email/' . $template_name . '.php';

		if ( ! file_exists( $template_full_path ) ) {
			return '';
		}
		$args = $args; // Resolve ( The method parameter $args is never used ) phpcs warning.
		require_once $template_full_path;

		return ob_get_clean();
	}

	/**
	 * Send email notification logic.
	 *
	 * @param int         $from_user_id User ID who initiated the action.
	 * @param int         $to_user_id   Recipient user ID.
	 * @param int         $course_id    Course ID related to the notification.
	 * @param string|null $due_date Optional course due date.
	 */
	public function send_email_notification( $from_user_id, $to_user_id, $course_id, $due_date = null ) {
		if ( ! $this->should_send_email_notification() ) {
			return;
		}

		$from_user      = get_userdata( $from_user_id );
		$from_user_name = $from_user->display_name;
		$to_user        = get_userdata( $to_user_id );
		$to_user_name   = $to_user->display_name;
		$course         = get_post( $course_id );
		$course_name    = ! empty( $course ) ? $course->post_title : '';
		$subject        = $this->email_subject( $course_name );
		$message        = $this->email_message( $from_user_name, $to_user_name, $course_id, $due_date );

		wp_mail(
			$to_user->user_email,
			wp_strip_all_tags( $subject ),
			$message,
			array( 'Content-Type: text/html; charset=UTF-8' )
		);
	}
}
