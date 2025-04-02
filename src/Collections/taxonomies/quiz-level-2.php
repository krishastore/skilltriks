<?php
/**
 * Quiz taxonomy.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\Taxonomies;

use const ST\Lms\STLMS_QUIZ_TAXONOMY_LEVEL_2;
use const ST\Lms\STLMS_QUIZ_CPT;

/**
 * Registers the `stlms_quiz_category` taxonomy,
 * for use with 'stlms_course'.
 */
function stlms_quiz_level_2_init() {
	register_taxonomy(
		STLMS_QUIZ_TAXONOMY_LEVEL_2,
		array( STLMS_QUIZ_CPT ),
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
				'name'                       => __( 'Category (Level 2)', 'skilltriks' ),
				'singular_name'              => _x( 'Category (Level 2)', 'taxonomy general name', 'skilltriks' ),
				'search_items'               => __( 'Search Category (Level 2)', 'skilltriks' ),
				'popular_items'              => __( 'Popular Category (Level 2)', 'skilltriks' ),
				'all_items'                  => __( 'All Category (Level 2)', 'skilltriks' ),
				'parent_item'                => __( 'Parent Category (Level 2)', 'skilltriks' ),
				'parent_item_colon'          => __( 'Parent Category (Level 2):', 'skilltriks' ),
				'edit_item'                  => __( 'Edit Category (Level 2)', 'skilltriks' ),
				'update_item'                => __( 'Update Category (Level 2)', 'skilltriks' ),
				'view_item'                  => __( 'View Category (Level 2)', 'skilltriks' ),
				'add_new_item'               => __( 'Add New Category (Level 2)', 'skilltriks' ),
				'new_item_name'              => __( 'New Category (Level 2)', 'skilltriks' ),
				'separate_items_with_commas' => __( 'Separate Category (Level 2) with commas', 'skilltriks' ),
				'add_or_remove_items'        => __( 'Add or remove Category (Level 2)', 'skilltriks' ),
				'choose_from_most_used'      => __( 'Choose from the most used Category (Level 2)', 'skilltriks' ),
				'not_found'                  => __( 'No Category (Level 2) found.', 'skilltriks' ),
				'no_terms'                   => __( 'No Category (Level 2)', 'skilltriks' ),
				'menu_name'                  => __( 'Categories', 'skilltriks' ),
				'items_list_navigation'      => __( 'Category (Level 2) list navigation', 'skilltriks' ),
				'items_list'                 => __( 'Category (Level 2) list', 'skilltriks' ),
				'most_used'                  => _x( 'Most Used', 'stlms_quiz_category', 'skilltriks' ),
				'back_to_items'              => __( '&larr; Back to Category (Level 2)', 'skilltriks' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => STLMS_QUIZ_TAXONOMY_LEVEL_2,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_quiz_level_2_init' );

/**
 * Sets the post updated messages for the `stlms_quiz_category` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_quiz_category` taxonomy.
 */
function stlms_quiz_level_2_updated_messages( $messages ) {

	$messages[ STLMS_QUIZ_TAXONOMY_LEVEL_2 ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Category (Level 2) added.', 'skilltriks' ),
		2 => __( 'Category (Level 2) deleted.', 'skilltriks' ),
		3 => __( 'Category (Level 2) updated.', 'skilltriks' ),
		4 => __( 'Category (Level 2) not added.', 'skilltriks' ),
		5 => __( 'Category (Level 2) not updated.', 'skilltriks' ),
		6 => __( 'Category (Level 2) deleted.', 'skilltriks' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\stlms_quiz_level_2_updated_messages' );
