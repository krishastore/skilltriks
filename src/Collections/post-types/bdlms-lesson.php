<?php
/**
 * Lesson post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_LESSON_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_lesson` post type.
 */
function stlms_lesson_init() {
	register_post_type(
		STLMS_LESSON_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Lessons', 'skilltriks-lms' ),
				'singular_name'         => __( 'Lesson', 'skilltriks-lms' ),
				'all_items'             => __( 'Lessons', 'skilltriks-lms' ),
				'archives'              => __( 'Lesson Archives', 'skilltriks-lms' ),
				'attributes'            => __( 'Lesson Attributes', 'skilltriks-lms' ),
				'insert_into_item'      => __( 'Insert into lesson', 'skilltriks-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this lesson', 'skilltriks-lms' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_lesson', 'skilltriks-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_lesson', 'skilltriks-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_lesson', 'skilltriks-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_lesson', 'skilltriks-lms' ),
				'filter_items_list'     => __( 'Filter Lessons list', 'skilltriks-lms' ),
				'items_list_navigation' => __( 'Lessons list navigation', 'skilltriks-lms' ),
				'items_list'            => __( 'Lessons list', 'skilltriks-lms' ),
				'new_item'              => __( 'New lesson', 'skilltriks-lms' ),
				'add_new'               => __( 'Add New', 'skilltriks-lms' ),
				'add_new_item'          => __( 'Add New lesson', 'skilltriks-lms' ),
				'edit_item'             => __( 'Edit lesson', 'skilltriks-lms' ),
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search Lessons', 'skilltriks-lms' ),
				'not_found'             => __( 'No Lessons found', 'skilltriks-lms' ),
				'not_found_in_trash'    => __( 'No Lessons found in trash', 'skilltriks-lms' ),
				'parent_item_colon'     => __( 'Parent lesson:', 'skilltriks-lms' ),
				'menu_name'             => __( 'Lessons', 'skilltriks-lms' ),
			),
			'publicly_queryable'    => false,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'revisions', 'author' ),
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\Lesson(), 'register_boxes' ),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_LESSON_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_lesson_init' );

/**
 * Sets the post updated messages for the `stlms_lesson` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_lesson` post type.
 */
function stlms_lesson_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_LESSON_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Lesson updated. <a target="_blank" href="%s">View lesson</a>', 'skilltriks-lms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'skilltriks-lms' ),
		3  => __( 'Custom field deleted.', 'skilltriks-lms' ),
		4  => __( 'Lesson updated.', 'skilltriks-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'lesson restored to revision from %s', 'skilltriks-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Lesson published. <a href="%s">View lesson</a>', 'skilltriks-lms' ), esc_url( $permalink ) ),
		7  => __( 'Lesson saved.', 'skilltriks-lms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Lesson submitted. <a target="_blank" href="%s">Preview lesson</a>', 'skilltriks-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Lesson scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview lesson</a>', 'skilltriks-lms' ), date_i18n( __( 'M j, Y @ G:i', 'skilltriks-lms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Lesson draft updated. <a target="_blank" href="%s">Preview lesson</a>', 'skilltriks-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_lesson_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_lesson` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_lesson` post type.
 */
function stlms_lesson_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_LESSON_CPT ] = array(
		/* translators: %s: Number of Lessons. */
		'updated'   => _n( '%s Lesson updated.', '%s Lessons updated.', $bulk_counts['updated'], 'skilltriks-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 lesson not updated, somebody is editing it.', 'skilltriks-lms' ) :
						/* translators: %s: Number of Lessons. */
						_n( '%s Lesson not updated, somebody is editing it.', '%s Lessons not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks-lms' ),
		/* translators: %s: Number of Lessons. */
		'deleted'   => _n( '%s Lesson permanently deleted.', '%s Lessons permanently deleted.', $bulk_counts['deleted'], 'skilltriks-lms' ),
		/* translators: %s: Number of Lessons. */
		'trashed'   => _n( '%s Lesson moved to the Trash.', '%s Lessons moved to the Trash.', $bulk_counts['trashed'], 'skilltriks-lms' ),
		/* translators: %s: Number of Lessons. */
		'untrashed' => _n( '%s Lesson restored from the Trash.', '%s Lessons restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_lesson_bulk_updated_messages', 10, 2 );
