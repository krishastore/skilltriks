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
use const ST\Lms\STLMS_NOTIFICATION_TABLE;
use const ST\Lms\STLMS_COURSE_CPT;

/**
 * Shortcode register manage class.
 */
class Notification extends \ST\Lms\Shortcode\Register {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'notifications' );
		add_action( 'after_delete_post', array( $this, 'remove_notification' ), 10, 2 );
		add_action( 'wp_ajax_stlms_read_notification', array( $this, 'read_notification' ) );
		add_action( 'init', array( $this, 'schedule_notification_cleanup' ) );
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

	/**
	 * Removes notification from DB when the course is deleted.
	 *
	 * @param int    $post_id id of the post.
	 * @param object $post post object.
	 */
	public function remove_notification( $post_id, $post ) {
		global $wpdb;

		if ( STLMS_COURSE_CPT === $post->post_type && empty( get_post( $post_id ) ) ) {
			$notifications_table = $wpdb->prefix . STLMS_NOTIFICATION_TABLE;

			$result = $wpdb->query( // phpcs:ignore.
				$wpdb->prepare(
					"DELETE FROM $notifications_table WHERE course_id = %d AND action_type != %d", // phpcs:ignore.
					$post_id,
					10
				)
			);

			if ( ! $result || is_wp_error( $result ) ) {
				EL::add(
					sprintf(
						'DB delete query failed for course ID %d. Error: %s',
						$post_id,
						$wpdb->last_error
					),
					'error',
					__FILE__,
					__LINE__
				);
			}
		}
	}

	/**
	 * Update notification table to make entry for read notification.
	 */
	public function read_notification() {
		global $wpdb;

		check_ajax_referer( STLMS_BASEFILE, '_nonce' );
		$data_id             = ! empty( $_POST['data_id'] ) ? (int) $_POST['data_id'] : 0;
		$type                = ! empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'single';
		$notifications_table = $wpdb->prefix . STLMS_NOTIFICATION_TABLE;
		$result              = 0;

		if ( 'single' === $type ) {
			if ( ! $data_id ) {
				return;
			}
			$result = $wpdb->update( // phpcs:ignore
				$notifications_table,
				array(
					'is_read' => 1,
				),
				array(
					'id' => $data_id,
				),
				array( '%d' ),
				array( '%d' )
			);
		} elseif ( 'all' === $type ) {
			$result = $wpdb->update( // phpcs:ignore
				$notifications_table,
				array(
					'is_read' => 1,
				),
				array(
					'to_user_id' => get_current_user_id(),
				),
				array( '%d' ),
				array( '%d' )
			);
		}

		if ( ! $result || is_wp_error( $result ) ) {
			EL::add(
				sprintf(
					'DB update query failed for read notification. Error: %s',
					$wpdb->last_error
				),
				'error',
				__FILE__,
				__LINE__
			);
		}
	}

	/**
	 * Schedule the daily cron event if not already scheduled.
	 */
	public function schedule_notification_cleanup() {
		if ( ! wp_next_scheduled( 'stlms_daily_notification_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'stlms_daily_notification_cleanup' );
		}

		add_action( 'stlms_daily_notification_cleanup', array( $this, 'run_notification_cleanup' ) );
	}

	/**
	 * Delete notifications older than 180 days.
	 */
	public function run_notification_cleanup() {
		global $wpdb;

		$notifications_table = $wpdb->prefix . STLMS_NOTIFICATION_TABLE;
		$cutoff              = gmdate( 'Y-m-d H:i:s', strtotime( '-180 days' ) );

		$wpdb->query( //phpcs:ignore.
			$wpdb->prepare(
				"DELETE FROM $notifications_table WHERE created_at < %s", //phpcs:ignore.
				$cutoff
			)
		);
	}
}
