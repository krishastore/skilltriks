<?php
/**
 * Quiz post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_QUIZ_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_quiz` post type.
 */
function stlms_quiz_init() {
	register_post_type(
		STLMS_QUIZ_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Quizzes', 'skilltriks-lms' ),
				'singular_name'         => __( 'Quiz', 'skilltriks-lms' ),
				'all_items'             => __( 'Quizzes', 'skilltriks-lms' ),
				'archives'              => __( 'Quiz Archives', 'skilltriks-lms' ),
				'attributes'            => __( 'Quiz Attributes', 'skilltriks-lms' ),
				'insert_into_item'      => __( 'Insert into quiz', 'skilltriks-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this quiz', 'skilltriks-lms' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_quiz', 'skilltriks-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_quiz', 'skilltriks-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_quiz', 'skilltriks-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_quiz', 'skilltriks-lms' ),
				'filter_items_list'     => __( 'Filter Quizzes list', 'skilltriks-lms' ),
				'items_list_navigation' => __( 'Quizzes list navigation', 'skilltriks-lms' ),
				'items_list'            => __( 'Quizzes list', 'skilltriks-lms' ),
				'new_item'              => __( 'New quiz', 'skilltriks-lms' ),
				'add_new'               => __( 'Add New', 'skilltriks-lms' ),
				'add_new_item'          => __( 'Add New quiz', 'skilltriks-lms' ),
				'edit_item'             => __( 'Edit quiz', 'skilltriks-lms' ),
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search Quizzes', 'skilltriks-lms' ),
				'not_found'             => __( 'No Quizzes found', 'skilltriks-lms' ),
				'not_found_in_trash'    => __( 'No Quizzes found in trash', 'skilltriks-lms' ),
				'parent_item_colon'     => __( 'Parent quiz:', 'skilltriks-lms' ),
				'menu_name'             => __( 'Quizzes', 'skilltriks-lms' ),
			),
			'publicly_queryable'    => false,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'revisions', 'author' ),
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\Quiz(), 'register_boxes' ),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_QUIZ_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_quiz_init' );

/**
 * Sets the post updated messages for the `stlms_quiz` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_quiz` post type.
 */
function stlms_quiz_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_QUIZ_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Quiz updated. <a target="_blank" href="%s">View quiz</a>', 'skilltriks-lms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'skilltriks-lms' ),
		3  => __( 'Custom field deleted.', 'skilltriks-lms' ),
		4  => __( 'Quiz updated.', 'skilltriks-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'quiz restored to revision from %s', 'skilltriks-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Quiz published. <a href="%s">View quiz</a>', 'skilltriks-lms' ), esc_url( $permalink ) ),
		7  => __( 'Quiz saved.', 'skilltriks-lms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Quiz submitted. <a target="_blank" href="%s">Preview quiz</a>', 'skilltriks-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Quiz scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview quiz</a>', 'skilltriks-lms' ), date_i18n( __( 'M j, Y @ G:i', 'skilltriks-lms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Quiz draft updated. <a target="_blank" href="%s">Preview quiz</a>', 'skilltriks-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_quiz_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_quiz` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_quiz` post type.
 */
function stlms_quiz_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_QUIZ_CPT ] = array(
		/* translators: %s: Number of Quizzes. */
		'updated'   => _n( '%s Quiz updated.', '%s Quizzes updated.', $bulk_counts['updated'], 'skilltriks-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 quiz not updated, somebody is editing it.', 'skilltriks-lms' ) :
						/* translators: %s: Number of Quizzes. */
						_n( '%s Quiz not updated, somebody is editing it.', '%s Quizzes not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks-lms' ),
		/* translators: %s: Number of Quizzes. */
		'deleted'   => _n( '%s Quiz permanently deleted.', '%s Quizzes permanently deleted.', $bulk_counts['deleted'], 'skilltriks-lms' ),
		/* translators: %s: Number of Quizzes. */
		'trashed'   => _n( '%s Quiz moved to the Trash.', '%s Quizzes moved to the Trash.', $bulk_counts['trashed'], 'skilltriks-lms' ),
		/* translators: %s: Number of Quizzes. */
		'untrashed' => _n( '%s Quiz restored from the Trash.', '%s Quizzes restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_quiz_bulk_updated_messages', 10, 2 );
