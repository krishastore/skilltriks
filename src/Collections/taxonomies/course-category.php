<?php
/**
 * Course taxonomy.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\Taxonomies;

use const ST\Lms\STLMS_COURSE_CATEGORY_TAX;
use const ST\Lms\STLMS_COURSE_CPT;

/**
 * Registers the `stlms_course_category` taxonomy,
 * for use with 'stlms_course'.
 */
function stlms_course_category_init() {
	register_taxonomy(
		STLMS_COURSE_CATEGORY_TAX,
		array( STLMS_COURSE_CPT ),
		array(
			'hierarchical'          => true,
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
				'name'                       => __( 'Course Categories', 'skilltriks-lms' ),
				'singular_name'              => _x( 'Course Categories', 'taxonomy general name', 'skilltriks-lms' ),
				'search_items'               => __( 'Search Course Categories', 'skilltriks-lms' ),
				'popular_items'              => __( 'Popular Course Categories', 'skilltriks-lms' ),
				'all_items'                  => __( 'All Course Categories', 'skilltriks-lms' ),
				'parent_item'                => __( 'Parent Course Categories', 'skilltriks-lms' ),
				'parent_item_colon'          => __( 'Parent Course Categories:', 'skilltriks-lms' ),
				'edit_item'                  => __( 'Edit Course Categories', 'skilltriks-lms' ),
				'update_item'                => __( 'Update Course Category', 'skilltriks-lms' ),
				'view_item'                  => __( 'View Course Categories', 'skilltriks-lms' ),
				'add_new_item'               => __( 'Add New Course Category', 'skilltriks-lms' ),
				'new_item_name'              => __( 'New Course Category', 'skilltriks-lms' ),
				'separate_items_with_commas' => __( 'Separate Course Categories with commas', 'skilltriks-lms' ),
				'add_or_remove_items'        => __( 'Add or remove Course Categories', 'skilltriks-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used Course Categories', 'skilltriks-lms' ),
				'not_found'                  => __( 'No course Categories found.', 'skilltriks-lms' ),
				'no_terms'                   => __( 'No Course Categories', 'skilltriks-lms' ),
				'menu_name'                  => __( 'Categories', 'skilltriks-lms' ),
				'items_list_navigation'      => __( 'Course Categories list navigation', 'skilltriks-lms' ),
				'items_list'                 => __( 'Course Categories list', 'skilltriks-lms' ),
				'most_used'                  => _x( 'Most Used', 'stlms_course_category', 'skilltriks-lms' ),
				'back_to_items'              => __( '&larr; Back to Course Categories', 'skilltriks-lms' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => STLMS_COURSE_CATEGORY_TAX,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_course_category_init' );

/**
 * Sets the post updated messages for the `stlms_course_category` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_course_category` taxonomy.
 */
function stlms_course_category_updated_messages( $messages ) {

	$messages[ STLMS_COURSE_CATEGORY_TAX ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Course category added.', 'skilltriks-lms' ),
		2 => __( 'Course category deleted.', 'skilltriks-lms' ),
		3 => __( 'Course category updated.', 'skilltriks-lms' ),
		4 => __( 'Course category not added.', 'skilltriks-lms' ),
		5 => __( 'Course category not updated.', 'skilltriks-lms' ),
		6 => __( 'Course category deleted.', 'skilltriks-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\stlms_course_category_updated_messages' );
