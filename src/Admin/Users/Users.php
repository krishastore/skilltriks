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
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
	}
}
