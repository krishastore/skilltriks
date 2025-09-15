<?php
/**
 * Notification class for due assigned course.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Notification;

use const ST\Lms\STLMS_COURSE_ASSIGN_TO_ME;
use const ST\Lms\STLMS_NOTIFICATION_TABLE;

/**
 * DueCourseNotification class.
 */
class DueCourseNotification extends \ST\Lms\Helpers\Notification {

	/**
	 * The main instance var.
	 *
	 * @var DueCourseNotification|null $instance The one DueCourseNotification instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return DueCourseNotification Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new DueCourseNotification();
		}
		return self::$instance;
	}

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'init', array( $this, 'schedule_due_course_notification' ) );
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
		$this->subject = __( 'Today’s the Day! Course: ', 'skilltriks' ) . $course_name . __( ' Is Due', 'skilltriks' );

		/**
		 * Filter the course assigned email subject.
		 *
		 * @param string $subject Email subject.
		 */
		return apply_filters( 'stlms/due_course_notification/subject', $this->subject );
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
			'due-course-email-template',
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
		return apply_filters( 'stlms/due_course_notification/message', $this->message );
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
		return apply_filters( 'stlms/due_course_notification/should_send_email', true );
	}

	/**
	 * Schedule the daily cron event if not already scheduled.
	 */
	public function schedule_due_course_notification() {
		if ( ! wp_next_scheduled( 'stlms_check_due_courses_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'stlms_check_due_courses_daily' );
		}

		add_action( 'stlms_check_due_courses_daily', array( $this, 'check_due_courses_daily' ) );
	}

	/**
	 * Check due courses and send email notifications.
	 */
	public function check_due_courses_daily() {
		global $wpdb;

		$today               = current_time( 'Y-m-d' );
		$notifications_table = $wpdb->prefix . STLMS_NOTIFICATION_TABLE;

		$users = get_users(
			array(
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_key'     => STLMS_COURSE_ASSIGN_TO_ME,
				'meta_compare' => 'EXISTS',
				'fields'       => 'ID',
			)
		);

		foreach ( $users as $user_id ) {
			$assigned_courses = get_user_meta( $user_id, STLMS_COURSE_ASSIGN_TO_ME, true );

			if ( ! is_array( $assigned_courses ) || empty( $assigned_courses ) ) {
				continue;
			}

			foreach ( $assigned_courses as $key => $due_date ) {
				if ( empty( $due_date ) || $due_date !== $today ) {
					continue;
				}

				list( $course_id, $from_user_id ) = explode( '_', $key, 2 );
				$to_user_id                       = $user_id;

				if ( $course_id && $from_user_id && $to_user_id ) {
					$this->send_email_notification( (int) $from_user_id, $to_user_id, (int) $course_id, $due_date, $is_assigner = false, 4 );

					$updated = $wpdb->update( // phpcs:ignore.
						$notifications_table,
						array( 'notification_sent' => 1 ),
						array(
							'action_type'  => 4,
							'to_user_id'   => $to_user_id,
							'from_user_id' => $from_user_id,
							'course_id'    => $course_id,
						),
						array( '%d' ),
						array( '%d', '%d', '%d', '%d' )
					);
				}
			}
		}
	}
}
