<?php
/**
 * Course department taxonomy.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\Taxonomies;

use const ST\Lms\STLMS_COURSE_TAXONOMY_DEP;
use const ST\Lms\STLMS_COURSE_CPT;

/**
 * Registers the `stlms_course_department` taxonomy,
 * for use with 'stlms_course'.
 */
function stlms_course_department_init() {
	register_taxonomy(
		STLMS_COURSE_TAXONOMY_DEP,
		array( STLMS_COURSE_CPT ),
		array(
			'hierarchical'          => false,
			'public'                => true,
			'show_in_nav_menus'     => true,
			'show_in_menu'          => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => true,
			'capabilities'          => array(
				'manage_terms' => 'edit_posts',
				'edit_terms'   => 'edit_posts',
				'delete_terms' => 'edit_posts',
				'assign_terms' => 'edit_posts',
			),
			'labels'                => array(
				'name'                       => __( 'Departments', 'skilltriks' ),
				'singular_name'              => _x( 'Departments', 'taxonomy general name', 'skilltriks' ),
				'search_items'               => __( 'Search Departments', 'skilltriks' ),
				'popular_items'              => __( 'Popular Departments', 'skilltriks' ),
				'all_items'                  => __( 'All Departments', 'skilltriks' ),
				'parent_item'                => __( 'Parent Department', 'skilltriks' ),
				'parent_item_colon'          => __( 'Parent Department:', 'skilltriks' ),
				'edit_item'                  => __( 'Edit Department', 'skilltriks' ),
				'update_item'                => __( 'Update Department', 'skilltriks' ),
				'view_item'                  => __( 'View Department', 'skilltriks' ),
				'add_new_item'               => __( 'Add New Department', 'skilltriks' ),
				'new_item_name'              => __( 'New Department', 'skilltriks' ),
				'separate_items_with_commas' => __( 'Separate Departments with commas', 'skilltriks' ),
				'add_or_remove_items'        => __( 'Add or remove Departments', 'skilltriks' ),
				'choose_from_most_used'      => __( 'Choose from the most used Departments', 'skilltriks' ),
				'not_found'                  => __( 'No Departments found.', 'skilltriks' ),
				'no_terms'                   => __( 'No Departments', 'skilltriks' ),
				'menu_name'                  => __( 'Departments', 'skilltriks' ),
				'items_list_navigation'      => __( 'Departments list navigation', 'skilltriks' ),
				'items_list'                 => __( 'Departments list', 'skilltriks' ),
				'most_used'                  => _x( 'Most Used', 'stlms_course_department', 'skilltriks' ),
				'back_to_items'              => __( '&larr; Back to Departments', 'skilltriks' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => STLMS_COURSE_TAXONOMY_DEP,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_course_department_init' );

/**
 * Sets the post updated messages for the `stlms_course_department` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_course_department` taxonomy.
 */
function stlms_course_department_updated_messages( $messages ) {

	$messages[ STLMS_COURSE_TAXONOMY_DEP ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Department added.', 'skilltriks' ),
		2 => __( 'Department deleted.', 'skilltriks' ),
		3 => __( 'Department updated.', 'skilltriks' ),
		4 => __( 'Department not added.', 'skilltriks' ),
		5 => __( 'Department not updated.', 'skilltriks' ),
		6 => __( 'Department deleted.', 'skilltriks' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\stlms_course_department_updated_messages' );

/**
 * Filters the result of `term_exists()` to provide a custom
 * "term already exists" error message for the stlms_course_department taxonomy.
 *
 * @param int|array|\WP_Error $term     The term ID, term object data, or WP_Error returned by `term_exists()`.
 * @param string              $taxonomy The taxonomy slug being checked.
 * @param array               $args     Array of arguments passed to `term_exists()`.
 *
 * @return int|array|\WP_Error Modified term value or WP_Error with a custom message.
 */
function stlms_modify_department_exists_msg( $term, $taxonomy, $args ) {

	if ( STLMS_COURSE_TAXONOMY_DEP !== $taxonomy ) {
		return $term;
	}

	$exists = term_exists( $term, $taxonomy, $args['parents'] );

	if ( ! empty( $exists ) ) {
		return new \WP_Error(
			'term_exists',
			/* translators: custom message when the department already exists. */
			__( 'This department name already exists. Please choose a different name.', 'skilltriks' ),
			$exists
		);
	}

	return $term;
}

add_filter( 'pre_insert_term', __NAMESPACE__ . '\\stlms_modify_department_exists_msg', 10, 3 );

/**
 * Remove the 'Count' column from the Department taxonomy list.
 *
 * @param array $columns An array of columns displayed in the list table.
 * @return array Modified columns array.
 */
function stlms_remove_department_count_column( $columns ) {
	if ( isset( $columns['posts'] ) ) {
		unset( $columns['posts'] );
	}
	return $columns;
}

add_filter( 'manage_edit-' . STLMS_COURSE_TAXONOMY_DEP . '_columns', __NAMESPACE__ . '\\stlms_remove_department_count_column' );
