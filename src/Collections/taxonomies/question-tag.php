<?php
/**
 * Question tag taxonomy.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\Taxonomies;

use const ST\Lms\STLMS_QUESTION_TAXONOMY_TAG;
use const ST\Lms\STLMS_QUESTION_CPT;

/**
 * Registers the `stlms_quesion_tag` taxonomy,
 * for use with 'stlms_question'.
 */
function stlms_quesion_tag_init() {
	register_taxonomy(
		STLMS_QUESTION_TAXONOMY_TAG,
		array( STLMS_QUESTION_CPT ),
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
				'name'                       => __( 'Topic', 'skilltriks-lms' ),
				'singular_name'              => _x( 'Topic', 'taxonomy general name', 'skilltriks-lms' ),
				'search_items'               => __( 'Search topic', 'skilltriks-lms' ),
				'popular_items'              => __( 'Popular topic', 'skilltriks-lms' ),
				'all_items'                  => __( 'All Topic', 'skilltriks-lms' ),
				'parent_item'                => __( 'Parent Topic', 'skilltriks-lms' ),
				'parent_item_colon'          => __( 'Parent Topic:', 'skilltriks-lms' ),
				'edit_item'                  => __( 'Edit Topic', 'skilltriks-lms' ),
				'update_item'                => __( 'Update Topic', 'skilltriks-lms' ),
				'view_item'                  => __( 'View Topic', 'skilltriks-lms' ),
				'add_new_item'               => __( 'Add New Topic', 'skilltriks-lms' ),
				'new_item_name'              => __( 'New Topic', 'skilltriks-lms' ),
				'separate_items_with_commas' => __( 'Separate topic with commas', 'skilltriks-lms' ),
				'add_or_remove_items'        => __( 'Add or remove topic', 'skilltriks-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used topic', 'skilltriks-lms' ),
				'not_found'                  => __( 'No topics found.', 'skilltriks-lms' ),
				'no_terms'                   => __( 'No topics', 'skilltriks-lms' ),
				'menu_name'                  => __( 'Topics', 'skilltriks-lms' ),
				'items_list_navigation'      => __( 'Topic list navigation', 'skilltriks-lms' ),
				'items_list'                 => __( 'Topic list', 'skilltriks-lms' ),
				'most_used'                  => _x( 'Most Used', 'stlms_quesion_tag', 'skilltriks-lms' ),
				'back_to_items'              => __( '&larr; Back to topic', 'skilltriks-lms' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => STLMS_QUESTION_TAXONOMY_TAG,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_quesion_tag_init' );

/**
 * Sets the post updated messages for the `stlms_quesion_tag` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_quesion_tag` taxonomy.
 */
function stlms_quesion_tag_updated_messages( $messages ) {

	$messages[ STLMS_QUESTION_TAXONOMY_TAG ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Topic added.', 'skilltriks-lms' ),
		2 => __( 'Topic deleted.', 'skilltriks-lms' ),
		3 => __( 'Topic updated.', 'skilltriks-lms' ),
		4 => __( 'Topic not added.', 'skilltriks-lms' ),
		5 => __( 'Topic not updated.', 'skilltriks-lms' ),
		6 => __( 'Topic deleted.', 'skilltriks-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\stlms_quesion_tag_updated_messages' );
