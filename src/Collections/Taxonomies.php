<?php
/**
 * The file that register the taxonomies.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace ST\Lms\Collections;

use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Register taxonomies.
 */
class Taxonomies {

	/**
	 * Taxonomies list.
	 *
	 * @var array $taxonomies
	 */
	private $taxonomies = array();

	/**
	 * Init hooks.
	 */
	public function init() {
		$this->register();
		add_filter( 'parent_file', array( $this, 'filter_parent_file' ) );
		add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );
	}

	/**
	 * Register taxonomies.
	 */
	private function register() {
		$this->taxonomies = apply_filters(
			'stlms/collections/taxonomies',
			glob( plugin_dir_path( __FILE__ ) . '/taxonomies/*.php' )
		);
		if ( ! empty( $this->taxonomies ) ) {
			foreach ( $this->taxonomies as $path ) {
				if ( is_readable( $path ) ) {
					require $path;
				}
			}
		}
	}

	/**
	 * Filter parent file hook.
	 *
	 * @param string $parent_file Parent file slug.
	 * @return string
	 */
	public function filter_parent_file( $parent_file ) {
		global $current_screen;
		$taxonomy = $current_screen->taxonomy;
		if ( in_array( $taxonomy, array( 'stlms_course_tag', 'stlms_course_category' ), true ) ) {
			$parent_file = PARENT_MENU_SLUG;
		}
		return $parent_file;
	}

	/**
	 * Register submenu item.
	 */
	public function register_submenu_page() {
		add_submenu_page(
			PARENT_MENU_SLUG,
			__( 'Categories', 'skilltriks-lms' ),
			__( 'Categories', 'skilltriks-lms' ),
			apply_filters( 'stlms/menu/capability', 'manage_options' ),
			'edit-tags.php?taxonomy=stlms_course_category',
			'__return_null'
		);
		add_submenu_page(
			PARENT_MENU_SLUG,
			__( 'Tags', 'skilltriks-lms' ),
			__( 'Tags', 'skilltriks-lms' ),
			apply_filters( 'stlms/menu/capability', 'manage_options' ),
			'edit-tags.php?taxonomy=stlms_course_tag',
			'__return_null'
		);
	}
}
