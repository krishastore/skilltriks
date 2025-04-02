<?php
/**
 * The file that defines the user management functionality.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin\Users
 */

namespace ST\Lms\Admin\Users;

use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Users manage class.
 */
class Users extends \ST\Lms\Admin\Core implements \ST\Lms\Interfaces\AdminCore {

	/**
	 * Store capability list class object.
	 *
	 * @var object|null $capability_list
	 * @since 1.0.0
	 */
	public $capability_list = null;

	/**
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
	}

	/**
	 * Registration of admin submenu.
	 */
	public function register_admin_menu() {
		$hook = add_submenu_page(
			'skilltriks',
			__( 'User Role Editor', 'skilltriks' ),
			__( 'User Role Editor', 'skilltriks' ),
			apply_filters( 'skilltriks/menu/capability', 'manage_options' ),
			'stlms_manage_roles',
			array( $this, 'render_menu_page' )
		);
		add_action( "load-$hook", array( $this, 'load_menu_page' ) );
	}

	/**
	 * Load submenu page..
	 */
	public function load_menu_page() {
		// Include WP_List_Table class file.
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		// Call the required model.
		$this->capability_list = new \ST\Lms\Admin\Users\CapabilityList();
	}

	/**
	 * Render admin menu page.
	 */
	public function render_menu_page() {
		require_once STLMS_TEMPLATEPATH . '/admin/users/capability-list.php';
	}
}
