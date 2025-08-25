<?php
/**
 * Notification class for course admin activity.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Notification;

use ST\Lms\ErrorLog as EL;
use const ST\Lms\META_KEY_COURSE_CURRICULUM;
use const ST\Lms\STLMS_NOTIFICATION_TABLE;
use const ST\Lms\STLMS_ENROL_COURSES;
use const ST\Lms\STLMS_LESSON_CPT;
use const ST\Lms\STLMS_QUIZ_CPT;
use const ST\Lms\STLMS_COURSE_CPT;
use const ST\Lms\META_KEY_LESSON_COURSE_IDS;
use const ST\Lms\META_KEY_LESSON_MEDIA;

/**
 * AdminActivityNotification class.
 */
class AdminActivityNotification extends \ST\Lms\Helpers\Notification {

	/**
	 * The main instance var.
	 *
	 * @var AdminActivityNotification|null $instance The one AdminActivityNotification instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return AdminActivityNotification Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new AdminActivityNotification();
		}
		return self::$instance;
	}

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'stlms_save_course_meta_before', array( $this, 'stlms_updated_course_content' ), 10, 2 );
		add_action( 'stlms_notify_course_content_changes', array( $this, 'notify_course_content_changes' ), 10, 3 );
		add_action( 'transition_post_status', array( $this, 'notify_course_status_changes' ), 10, 3 );
		add_action( 'stlms_save_lesson_meta_before', array( $this, 'notify_lesson_content_changes' ), 10, 2 );
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
		return '';
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
		return '';
	}

	/**
	 * Return true if this notification should also send an email.
	 *
	 * @return bool
	 */
	public function should_send_email_notification() {
		return false;
	}

	/**
	 * Compare old and new course curriculum and detect added/removed lessons/quizzes.
	 *
	 * @param int   $post_id  The course post ID.
	 * @param array $new_data The new course data from save request.
	 */
	public function stlms_updated_course_content( $post_id, $new_data ) {

		if ( empty( $new_data['curriculum'] ) || ! is_array( $new_data['curriculum'] ) ) {
			return array();
		}

		$old_data = get_post_meta( $post_id, META_KEY_COURSE_CURRICULUM, true );
		$old_data = maybe_unserialize( $old_data );
		$old_data = is_array( $old_data ) ? $old_data : array();

		$extract_items = function ( $curriculum ) {
			$items = array();
			if ( is_array( $curriculum ) ) {
				foreach ( $curriculum as $section ) {
					if ( ! empty( $section['items'] ) && is_array( $section['items'] ) ) {
						$items = array_merge( $items, $section['items'] );
					}
				}
			}
			return array_unique( $items );
		};

		$old_items = $extract_items( $old_data );
		$new_items = $extract_items( $new_data['curriculum'] );

		$added   = array_diff( $new_items, $old_items );
		$removed = array_diff( $old_items, $new_items );

		$changes = array(
			'lesson_added'   => array(),
			'lesson_removed' => array(),
			'quiz_added'     => array(),
			'quiz_removed'   => array(),
		);

		foreach ( $added as $id ) {
			if ( STLMS_LESSON_CPT === get_post_type( $id ) ) {
				$lessons = get_post_meta( $id, META_KEY_LESSON_COURSE_IDS, true ) ? get_post_meta( $id, META_KEY_LESSON_COURSE_IDS, true ) : array();

				if ( ! in_array( $post_id, $lessons, true ) ) {
					$lessons[] = $post_id;
					update_post_meta( $id, META_KEY_LESSON_COURSE_IDS, $lessons );
				}

				$changes['lesson_added'][] = $id;

			} elseif ( STLMS_QUIZ_CPT === get_post_type( $id ) ) {
				$changes['quiz_added'][] = $id;
			}
		}

		foreach ( $removed as $id ) {
			if ( STLMS_LESSON_CPT === get_post_type( $id ) ) {
				$lessons = get_post_meta( $id, META_KEY_LESSON_COURSE_IDS, true ) ? get_post_meta( $id, META_KEY_LESSON_COURSE_IDS, true ) : array();
				$key     = array_search( $post_id, $lessons, true );

				if ( false !== $key ) {
					unset( $lessons[ $key ] );
					$lessons = array_values( $lessons );
					update_post_meta( $id, META_KEY_LESSON_COURSE_IDS, $lessons );
				}

				$changes['lesson_removed'][] = $id;
			} elseif ( STLMS_QUIZ_CPT === get_post_type( $id ) ) {
				$changes['quiz_removed'][] = $id;
			}
		}

		$changes   = array_filter( $changes );
		$author_id = (int) get_post_field( 'post_author', $post_id );

		if ( ! empty( $changes ) ) {
			$cron_hook = 'stlms_notify_course_content_changes';
			$args      = array( $post_id, $changes, $author_id );
			$run_time  = strtotime( '+1 minutes', time() );
			if ( ! wp_next_scheduled( $cron_hook, $args ) ) {
				wp_schedule_single_event( $run_time, $cron_hook, $args );
				EL::add( sprintf( 'Cron schedule at: %s, cron hook: %s', $run_time, $cron_hook ), 'info', __FILE__, __LINE__ );
			}
		}
	}

	/**
	 * Compare old and new course curriculum and detect added/removed lessons/quizzes.
	 *
	 * @param int   $course_id  The course post ID.
	 * @param array $changes The new course data from save request.
	 * @param int   $author_id  Author who updated the course.
	 */
	public function notify_course_content_changes( $course_id, $changes, $author_id ) {
		if ( false === get_post_status( $course_id ) ) {
			return;
		}

		$this->stlms_store_notifications_data( $course_id, $changes, 8 );
	}

	/**
	 * Handle course status changes and send notifications accordingly.
	 *
	 * This function hooks into the 'transition_post_status' action and determines
	 * when to send notifications based on post status changes.
	 *
	 *  1. Send notification when a course is published, but NOT when it's first created/published.
	 *  2. Send notification when a course transitions from 'publish' to any other status.
	 *
	 * @param string $new_status The new status of the post (e.g. 'publish', 'draft', 'pending').
	 * @param string $old_status The old status of the post (e.g. 'draft', 'publish').
	 * @param object $post       The post object being transitioned.
	 */
	public function notify_course_status_changes( $new_status, $old_status, $post ) {
		// Bail out if not course.
		if ( STLMS_COURSE_CPT !== $post->post_type ) {
			return;
		}

		// Bail out if status is not changed.
		if ( $old_status === $new_status ) {
			return;
		}

		$action_type = 0;
		$title       = null;

		if ( 'publish' === $new_status && 'publish' !== $old_status ) {
			$action_type = 9;
		}

		if ( 'publish' === $old_status && 'publish' !== $new_status ) {
			$action_type = 10;
			$title       = get_the_title( $post->ID );
		}

		// Bail out if course status is changed internally instead of publish.
		if ( ! $action_type ) {
			return;
		}

		$this->stlms_store_notifications_data( $post->ID, $title, $action_type );
	}

	/**
	 * Compare old and new lesson media and detect added/removed videos/files.
	 *
	 * @param int   $lesson_id  The lesson post ID.
	 * @param array $meta_data The new meta data from save request.
	 */
	public function notify_lesson_content_changes( $lesson_id, $meta_data ) {
		if ( empty( $meta_data['media'] ) ) {
			return;
		}

		$existing_meta = get_post_meta( $lesson_id, META_KEY_LESSON_MEDIA, true );
		$course_ids    = get_post_meta( $lesson_id, META_KEY_LESSON_COURSE_IDS, true );

		if ( empty( $course_ids ) ) {
			return;
		}

		if ( empty( $existing_meta ) || ! is_array( $existing_meta ) ) {
			foreach ( $course_ids as $course_id ) {
				$this->stlms_store_notifications_data( $lesson_id, null, 11, $course_id );
			}
			return;
		}

		$keys_to_check = array( 'media_type', 'video_id', 'file_id', 'file_url', 'embed_video_url' );

		$existing_data = array_intersect_key( $existing_meta, array_flip( $keys_to_check ) );
		$changed_data  = array_intersect_key( $meta_data['media'], array_flip( $keys_to_check ) );

		if ( $existing_data !== $changed_data ) {
			foreach ( $course_ids as $course_id ) {
				$this->stlms_store_notifications_data( $lesson_id, null, 11, $course_id );
			}
		}
	}

	/**
	 * Store notifications data.
	 *
	 * @param int               $content_id  The ID of the course,lesson.
	 * @param array|null|string $content_changes The content changed when the course/lesson is updated.
	 * @param int               $action_type  The action type.
	 * @param int               $course_id  The course ID.
	 */
	private function stlms_store_notifications_data( $content_id, $content_changes, $action_type, $course_id = 0 ) {
		global $wpdb;

		$author_id           = (int) get_post_field( 'post_author', $content_id );
		$notifications_table = $wpdb->prefix . STLMS_NOTIFICATION_TABLE;
		$content_changes     = ! empty( $content_changes ) ? wp_json_encode( $content_changes ) : null;
		$course_id           = $course_id ? $course_id : $content_id;

		$user_query = new \WP_User_Query(
			array(
				'meta_key'     => STLMS_ENROL_COURSES, //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'   => (string) $course_id, //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_compare' => 'LIKE',
				'fields'       => 'ID',
			)
		);
		$user_ids   = $user_query->get_results();

		foreach ( array_chunk( $user_ids, 50 ) as $batch ) {
			foreach ( $batch as $user_id ) {

				$to_user      = get_userdata( $user_id );
				$to_user_name = $to_user->display_name;

				$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$notifications_table,
					array(
						'from_user_id'      => $author_id,
						'to_user_id'        => $user_id,
						'course_id'         => $content_id,
						'due_date'          => '0000-00-00',
						'action_type'       => $action_type,
						'is_read'           => 0,
						'notification_sent' => 1,
						'content_changes'   => $content_changes,
					),
					array( '%d', '%d', '%d', '%s', '%d', '%d', '%d', '%s' )
				);

				delete_transient( 'stlms_notification_data_' . $user_id );

				if ( ! $result || is_wp_error( $result ) ) {
					EL::add(
						sprintf(
							'DB insert failed for user ID %d, course ID %d. Error: %s',
							$user_id,
							$content_id,
							$wpdb->last_error
						),
						'error',
						__FILE__,
						__LINE__
					);
				}
			}
		}
	}
}
