<?php
/**
 * The file that manage the database related events.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 */

namespace ST\Lms\Helpers;

use ST\Lms\ErrorLog as EL;

/**
 * Helpers utility class.
 */
class Utility implements \ST\Lms\Interfaces\Helpers {

	/**
	 * Default pages used by LP
	 *
	 * @var array
	 */
	private static $pages = array(
		'login',
		'courses',
		'term_conditions',
		'my_learning',
		'assign_new_course',
		'assign_course_to_me',
		'assign_course_by_me',
		'notifications',
	);

	/**
	 * On plugin activation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activation_hook() {
		self::create_default_roles();
		self::create_pages();
		self::stlms_custom_table();
		self::activate_default_layout();
	}

	/**
	 * On plugin deactivation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivation_hook() {
		$pages = self::$pages;
		try {
			foreach ( $pages as $page ) {
				$option_key = "stlms_{$page}_page_id";
				$page_id    = (int) get_option( $option_key, false );
				if ( empty( $page_id ) ) {
					continue;
				}
				wp_delete_post( $page_id, true );
				delete_option( $option_key );
			}
			delete_option( 'stlms_permalinks_flushed' );
			wp_clear_scheduled_hook( 'stlms_check_due_courses_daily' );
			wp_clear_scheduled_hook( 'stlms_check_over_due_courses_daily' );
			wp_clear_scheduled_hook( 'stlms_check_due_soon_courses_daily' );
			wp_clear_scheduled_hook( 'stlms_notify_course_content_changes' );
			wp_clear_scheduled_hook( 'stlms_daily_notification_cleanup' );
		} catch ( \Exception $ex ) {
			EL::add( $ex->getMessage() );
		}
	}

	/**
	 * Create default pages..
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_pages() {
		$pages = self::$pages;
		try {
			foreach ( $pages as $page ) {
				// Check if page has already existed.
				$page_id = get_option( "stlms_{$page}_page_id", false );

				if ( $page_id && 'page' === get_post_type( $page_id ) && 'publish' === get_post_status( $page_id ) ) {
					continue;
				}

				if ( 'courses' === $page ) {
					$page_title = 'All Courses';
					$page_slug  = $page;
				} else {
					$page_title = ucwords( str_replace( '_', ' ', $page ) );
					$page_slug  = 'stlms-' . str_replace( '_', '-', $page );
				}

				$data_create_page = array(
					'post_title' => $page_title,
					'post_name'  => $page_slug,
				);
				self::create_page( $data_create_page, "stlms_{$page}_page_id" );
			}

			flush_rewrite_rules();
		} catch ( \Exception $ex ) {
			EL::add( $ex->getMessage() );
		}
	}

	/**
	 * Create LP static page.
	 *
	 * @param array  $args Custom args.
	 * @param string $key_option Global option key.
	 * @throws \Exception Errors.
	 *
	 * @return bool|int
	 */
	public static function create_page( $args = array(), $key_option = '' ) {
		$page_id = 0;

		try {
			if ( ! isset( $args['post_title'] ) ) {
				throw new \Exception( __( 'Missing post title', 'skilltriks' ) );
			}

			if ( preg_match( '#^stlms_login_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_login]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^stlms_courses_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_courses filter="yes" pagination="yes"]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^stlms_my_learning_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_my_learning filter="yes" pagination="yes"]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^stlms_assign_new_course_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_assign_new_course]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^stlms_assign_course_to_me_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_assign_course_to_me]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^stlms_assign_course_by_me_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_assign_course_by_me]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^stlms_notifications_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[stlms_notifications]<!-- /wp:shortcode -->';
			}

			$args = array_merge(
				array(
					'post_title'     => '',
					'post_name'      => '',
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'post_content'   => '',
					'post_author'    => get_current_user_id(),
				),
				$args
			);

			$page_id = wp_insert_post( $args );

			if ( ! is_int( $page_id ) ) {
				return 0;
			}

			update_option( $key_option, $page_id );
		} catch ( \Throwable $e ) {
			EL::add( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $page_id;
	}

	/**
	 * Create default roles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_default_roles() {
		add_role(
			'stlms',
			esc_html__( 'SkillTriks LMS', 'skilltriks' ),
			array(
				'read'    => true,
				'level_0' => true,
			)
		);
	}

	/**
	 * Create a table to store cron data.
	 *
	 * @throws \Exception Errors.
	 */
	public static function stlms_custom_table() {
		global $wpdb;

		$tables = array(
			$wpdb->prefix . \ST\Lms\STLMS_CRON_TABLE => 'CREATE TABLE %s (
				id INT(11) NOT NULL AUTO_INCREMENT,
				attachment_id INT(11) NOT NULL,
				file_name VARCHAR(255) NOT NULL,
				progress INT(11) NOT NULL,
				import_status INT(11) NOT NULL,
				import_type INT(11) NOT NULL,
				total_rows INT(11) NOT NULL,
				success_rows INT(11) NOT NULL,
				fail_rows INT(11) NOT NULL,
				import_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			)',

			$wpdb->prefix . \ST\Lms\STLMS_NOTIFICATION_TABLE => 'CREATE TABLE %s (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				from_user_id BIGINT(20) UNSIGNED NOT NULL,
				to_user_id BIGINT(20) UNSIGNED NOT NULL,
				course_id BIGINT(20) UNSIGNED NOT NULL,
				due_date DATE DEFAULT NULL,
				action_type TINYINT(1) NOT NULL COMMENT \'1=assigned, 2=updated, 3=removed, 4=due, 5=due_soon, 6=over_due, 7=completed, 8=content_changes\',
				content_changes JSON DEFAULT NULL,
				is_read TINYINT(1) DEFAULT 0,
				notification_sent TINYINT(1) DEFAULT 0,
				created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY user_date (to_user_id, created_at)
			)',
		);

		require_once ABSPATH . '/wp-admin/includes/upgrade.php';
		$charset_collate = $wpdb->get_charset_collate();

		foreach ( $tables as $table_name => $table_sql_template ) {
			$sql = sprintf( $table_sql_template, $table_name ) . " $charset_collate;";
			dbDelta( $sql );
		}
	}

	/**
	 * Activate default layout.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate_default_layout() {
		$stlms_settings          = get_option( 'stlms_settings', array() );
		$stlms_settings['theme'] = 'layout-default';
		update_option( 'stlms_settings', $stlms_settings );
	}
}
