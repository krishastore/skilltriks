<?php
/**
 * Declare the interface for `ST\Lms\Admin\Core` class.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 */

namespace ST\Lms\Interfaces;

interface AdminCore {

	/**
	 * Register admin menu.
	 */
	public function register_admin_menu();

	/**
	 * Render admin page.
	 */
	public function render_menu_page();
}
