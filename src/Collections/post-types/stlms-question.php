<?php
/**
 * Question post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_QUESTION_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_question` post type.
 */
function stlms_question_init() {
	register_post_type(
		STLMS_QUESTION_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Question Bank', 'skilltriks-lms' ),
				'singular_name'         => __( 'Question', 'skilltriks-lms' ),
				'all_items'             => __( 'Questions', 'skilltriks-lms' ),
				'archives'              => __( 'Question Archives', 'skilltriks-lms' ),
				'attributes'            => __( 'Question Attributes', 'skilltriks-lms' ),
				'insert_into_item'      => __( 'Insert into question', 'skilltriks-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this question', 'skilltriks-lms' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_question', 'skilltriks-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_question', 'skilltriks-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_question', 'skilltriks-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_question', 'skilltriks-lms' ),
				'filter_items_list'     => __( 'Filter question list', 'skilltriks-lms' ),
				'items_list_navigation' => __( 'Questions list navigation', 'skilltriks-lms' ),
				'items_list'            => __( 'Questions list', 'skilltriks-lms' ),
				'new_item'              => __( 'New question', 'skilltriks-lms' ),
				'add_new'               => __( 'Add New', 'skilltriks-lms' ),
				'add_new_item'          => __( 'Add New Question', 'skilltriks-lms' ),
				'edit_item'             => __( 'Edit question', 'skilltriks-lms' ),
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search questions', 'skilltriks-lms' ),
				'not_found'             => __( 'No questions found', 'skilltriks-lms' ),
				'not_found_in_trash'    => __( 'No questions found in trash', 'skilltriks-lms' ),
				'parent_item_colon'     => __( 'Parent question:', 'skilltriks-lms' ),
				'menu_name'             => __( 'Questions', 'skilltriks-lms' ),
			),
			'publicly_queryable'    => false,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'revisions', 'author' ),
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\QuestionBank(), 'register_boxes' ),
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_QUESTION_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_question_init' );

/**
 * Sets the post updated messages for the `stlms_question` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_question` post type.
 */
function stlms_question_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_QUESTION_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => __( 'Question updated.', 'skilltriks-lms' ),
		2  => __( 'Custom field updated.', 'skilltriks-lms' ),
		3  => __( 'Custom field deleted.', 'skilltriks-lms' ),
		4  => __( 'Question updated.', 'skilltriks-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Question restored to revision from %s', 'skilltriks-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => __( 'Question published.', 'skilltriks-lms' ),
		7  => __( 'Question saved.', 'skilltriks-lms' ),
		/* translators: %s: post permalink */
		8  => __( 'Question submitted.', 'skilltriks-lms' ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Question scheduled for: <strong>%1$s</strong>.', 'skilltriks-lms' ), date_i18n( __( 'M j, Y @ G:i', 'skilltriks-lms' ), strtotime( $post->post_date ) ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Question draft updated.', 'skilltriks-lms' ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_question_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_question` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_question` post type.
 */
function stlms_question_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_QUESTION_CPT ] = array(
		/* translators: %s: Number of Questions. */
		'updated'   => _n( '%s Question updated.', '%s Questions updated.', $bulk_counts['updated'], 'skilltriks-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Question not updated, somebody is editing it.', 'skilltriks-lms' ) :
						/* translators: %s: Number of Questions. */
						_n( '%s Question not updated, somebody is editing it.', '%s Questions not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks-lms' ),
		/* translators: %s: Number of Questions. */
		'deleted'   => _n( '%s question permanently deleted.', '%s Questions permanently deleted.', $bulk_counts['deleted'], 'skilltriks-lms' ),
		/* translators: %s: Number of Questions. */
		'trashed'   => _n( '%s question moved to the Trash.', '%s Questions moved to the Trash.', $bulk_counts['trashed'], 'skilltriks-lms' ),
		/* translators: %s: Number of Questions. */
		'untrashed' => _n( '%s question restored from the Trash.', '%s Questions restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_question_bulk_updated_messages', 10, 2 );
