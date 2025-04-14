<?php
/**
 * Question tag taxonomy.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\Taxonomies;

use const ST\Lms\STLMS_LESSON_TAXONOMY_TAG;
use const ST\Lms\STLMS_LESSON_CPT;

/**
 * Registers the `stlms_lesson_topics` taxonomy,
 * for use with 'stlms_lesson'.
 */
function lesson_init() {
	register_taxonomy(
		STLMS_LESSON_TAXONOMY_TAG,
		array( STLMS_LESSON_CPT ),
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
				'name'                       => __( 'Topic', 'skilltriks' ),
				'singular_name'              => _x( 'Topic', 'taxonomy general name', 'skilltriks' ),
				'search_items'               => __( 'Search topic', 'skilltriks' ),
				'popular_items'              => __( 'Popular topic', 'skilltriks' ),
				'all_items'                  => __( 'All Topic', 'skilltriks' ),
				'parent_item'                => __( 'Parent Topic', 'skilltriks' ),
				'parent_item_colon'          => __( 'Parent Topic:', 'skilltriks' ),
				'edit_item'                  => __( 'Edit Topic', 'skilltriks' ),
				'update_item'                => __( 'Update Topic', 'skilltriks' ),
				'view_item'                  => __( 'View Topic', 'skilltriks' ),
				'add_new_item'               => __( 'Add New Topic', 'skilltriks' ),
				'new_item_name'              => __( 'New Topic', 'skilltriks' ),
				'separate_items_with_commas' => __( 'Separate topic with commas', 'skilltriks' ),
				'add_or_remove_items'        => __( 'Add or remove topic', 'skilltriks' ),
				'choose_from_most_used'      => __( 'Choose from the most used topic', 'skilltriks' ),
				'not_found'                  => __( 'No topics found.', 'skilltriks' ),
				'no_terms'                   => __( 'No topics', 'skilltriks' ),
				'menu_name'                  => __( 'Topics', 'skilltriks' ),
				'items_list_navigation'      => __( 'Topic list navigation', 'skilltriks' ),
				'items_list'                 => __( 'Topic list', 'skilltriks' ),
				'most_used'                  => _x( 'Most Used', 'stlms_lesson_topics', 'skilltriks' ),
				'back_to_items'              => __( '&larr; Back to topic', 'skilltriks' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => STLMS_LESSON_TAXONOMY_TAG,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\lesson_init' );

/**
 * Sets the post updated messages for the `stlms_lesson_topics` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_lesson_topics` taxonomy.
 */
function lesson_updated_messages( $messages ) {

	$messages[ STLMS_LESSON_TAXONOMY_TAG ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Topic added.', 'skilltriks' ),
		2 => __( 'Topic deleted.', 'skilltriks' ),
		3 => __( 'Topic updated.', 'skilltriks' ),
		4 => __( 'Topic not added.', 'skilltriks' ),
		5 => __( 'Topic not updated.', 'skilltriks' ),
		6 => __( 'Topic deleted.', 'skilltriks' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\lesson_updated_messages' );
