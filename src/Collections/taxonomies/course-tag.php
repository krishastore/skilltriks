<?php
/**
 * Course Level taxonomy.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\Taxonomies;

use const ST\Lms\STLMS_COURSE_TAXONOMY_TAG;
use const ST\Lms\STLMS_COURSE_CPT;

/**
 * Registers the `stlms_course_tag` taxonomy,
 * for use with 'stlms_course'.
 */
function stlms_course_tag_init() {
	register_taxonomy(
		STLMS_COURSE_TAXONOMY_TAG,
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
				'name'                       => __( 'Course Levels', 'skilltriks' ),
				'singular_name'              => _x( 'Course Levels', 'taxonomy general name', 'skilltriks' ),
				'search_items'               => __( 'Search Course Levels', 'skilltriks' ),
				'popular_items'              => __( 'Popular Course Levels', 'skilltriks' ),
				'all_items'                  => __( 'All Course Levels', 'skilltriks' ),
				'parent_item'                => __( 'Parent Level', 'skilltriks' ),
				'parent_item_colon'          => __( 'Parent Level:', 'skilltriks' ),
				'edit_item'                  => __( 'Edit Course Level', 'skilltriks' ),
				'update_item'                => __( 'Update Level', 'skilltriks' ),
				'view_item'                  => __( 'View Level', 'skilltriks' ),
				'add_new_item'               => __( 'Add New Level', 'skilltriks' ),
				'new_item_name'              => __( 'New Level', 'skilltriks' ),
				'separate_items_with_commas' => __( 'Separate Course Levels with commas', 'skilltriks' ),
				'add_or_remove_items'        => __( 'Add or remove Course Levels', 'skilltriks' ),
				'choose_from_most_used'      => __( 'Choose from the most used Course Levels', 'skilltriks' ),
				'not_found'                  => __( 'No levels found.', 'skilltriks' ),
				'no_terms'                   => __( 'No Levels', 'skilltriks' ),
				'menu_name'                  => __( 'Tags', 'skilltriks' ),
				'items_list_navigation'      => __( 'Course Levels list navigation', 'skilltriks' ),
				'items_list'                 => __( 'Course Levels list', 'skilltriks' ),
				'most_used'                  => _x( 'Most Used', 'stlms_course_tag', 'skilltriks' ),
				'back_to_items'              => __( '&larr; Back to Course Levels', 'skilltriks' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => STLMS_COURSE_TAXONOMY_TAG,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_course_tag_init' );

/**
 * Sets the post updated messages for the `stlms_course_tag` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_course_tag` taxonomy.
 */
function stlms_course_tag_updated_messages( $messages ) {

	$messages[ STLMS_COURSE_TAXONOMY_TAG ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Level added.', 'skilltriks' ),
		2 => __( 'Level deleted.', 'skilltriks' ),
		3 => __( 'Level updated.', 'skilltriks' ),
		4 => __( 'Level not added.', 'skilltriks' ),
		5 => __( 'Level not updated.', 'skilltriks' ),
		6 => __( 'Level deleted.', 'skilltriks' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\stlms_course_tag_updated_messages' );
