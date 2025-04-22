<?php
/**
 * The file that manage the user capabilities.
 *
 * @link       https://getskilltriks.com
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin\Users
 */

namespace ST\Lms\Admin\Users;

use ST\Lms\ErrorLog as EL;

/**
 * Capability manage class.
 */
class Capability extends \WP_List_Table {

	/**
	 * Pagination per page.
	 *
	 * @var int $per_page.
	 */
	public $per_page;

	/**
	 * Public constructor.
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct();

		$this->per_page = 20;
		$this->setting_enqueue_scripts();
		$this->handle_delete_action();
	}

	/**
	 * Entry Data
	 *
	 * @param string $search_by_name Role search by name.
	 * @return array|null
	 */
	public function get_roles( $search_by_name ) {
		$per_page = $this->per_page;
		$settings = get_option( 'stlms_settings' );
		$data     = isset( $settings['user_role'] ) && ! empty( $settings['user_role'] ) ? $settings['user_role'] : array();

		if ( ! empty( $search_by_name ) ) {
			$result = array_filter(
				$data,
				function ( $value ) use ( $search_by_name ) {
					return $value == $search_by_name; //phpcs:ignore.Universal.Operators.StrictComparisons.LooseEqual
				},
				ARRAY_FILTER_USE_BOTH
			);
			return $result;
		}
		return $data;
	}

	/**
	 * Enqueue setting scripts and styles.
	 */
	public function setting_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_style( \ST\Lms\STLMS_SETTING );
		wp_enqueue_script( \ST\Lms\STLMS_SETTING );
	}

	/**
	 * Removes the additional user capability from the user on removal of role.
	 *
	 * @param string $role_id Role name.
	 */
	protected function remove_additional_user_caps( $role_id ) {
		$args  = array(
			'role'   => $role_id,
			'fields' => 'all',
		);
		$users = get_users( $args );

		foreach ( $users as $user ) {
			if ( $user->has_cap( $role_id ) ) {
				$user->remove_cap( $role_id );
			}
		}
	}

	/**
	 * Handle delete role action
	 */
	protected function handle_delete_action() {
		if ( isset( $_GET['action'] ) && 'delete_role' === $_GET['action'] && isset( $_GET['id'] ) ) {
			// security check!
			if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

				$nonce  = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
				$action = 'delete_role';

				if ( ! wp_verify_nonce( $nonce, $action ) ) {
					EL::add( 'Failed nonce verification', 'error', __FILE__, __LINE__ );
					return;
				}
			}

			$role_id     = sanitize_text_field( wp_unslash( $_GET['id'] ) );
			$role_id     = preg_replace( '/-/', '_', $role_id );
			$role_exists = get_option( 'stlms_settings', array() );
			if ( ! empty( $role_id ) && array_key_exists( $role_id, $role_exists['user_role'] ) ) {
				if ( ! empty( get_role( $role_id ) ) ) {
					$this->remove_additional_user_caps( $role_id );
					remove_role( $role_id );
				}
				unset( $role_exists['user_role'][ $role_id ] );
				update_option( 'stlms_settings', $role_exists );
			}

			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
			wp_redirect( add_query_arg( 'page', 'stlms_manage_roles', admin_url( 'admin.php' ) ) );
			exit;
		}
	}
}
