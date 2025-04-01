<?php
/**
 * Course post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_COURSE_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_course` post type.
 */
function stlms_course_init() {
	register_post_type(
		STLMS_COURSE_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Courses', 'skilltriks-lms' ),
				'singular_name'         => __( 'Course', 'skilltriks-lms' ),
				'all_items'             => __( 'Courses', 'skilltriks-lms' ),
				'archives'              => __( 'Course Archives', 'skilltriks-lms' ),
				'attributes'            => __( 'Course Attributes', 'skilltriks-lms' ),
				'insert_into_item'      => __( 'Insert into Course', 'skilltriks-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Course', 'skilltriks-lms' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_course', 'skilltriks-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_course', 'skilltriks-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_course', 'skilltriks-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_course', 'skilltriks-lms' ),
				'filter_items_list'     => __( 'Filter Courses list', 'skilltriks-lms' ),
				'items_list_navigation' => __( 'Courses list navigation', 'skilltriks-lms' ),
				'items_list'            => __( 'Courses list', 'skilltriks-lms' ),
				'new_item'              => __( 'New Course', 'skilltriks-lms' ),
				'add_new'               => __( 'Add New', 'skilltriks-lms' ),
				'add_new_item'          => __( 'Add New Course', 'skilltriks-lms' ),
				'edit_item'             => __( 'Edit Course', 'skilltriks-lms' ),
				'view_item'             => __( 'View Course', 'skilltriks-lms' ),
				'view_items'            => __( 'View Courses', 'skilltriks-lms' ),
				'search_items'          => __( 'Search Courses', 'skilltriks-lms' ),
				'not_found'             => __( 'No courses found', 'skilltriks-lms' ),
				'not_found_in_trash'    => __( 'No Courses found in trash', 'skilltriks-lms' ),
				'parent_item_colon'     => __( 'Parent Course:', 'skilltriks-lms' ),
				'menu_name'             => __( 'Courses', 'skilltriks-lms' ),
			),
			'publicly_queryable'    => true,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'excerpt' ),
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\Course(), 'register_boxes' ),
			'has_archive'           => false,
			'rewrite'               => array(
				'slug'       => \ST\Lms\get_page_url( 'courses', true ),
				'with_front' => false,
			),
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_COURSE_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_course_init' );

/**
 * Sets the post updated messages for the `stlms_course` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_course` post type.
 */
function stlms_course_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_COURSE_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Course updated. <a target="_blank" href="%s">View Course</a>', 'skilltriks-lms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'skilltriks-lms' ),
		3  => __( 'Custom field deleted.', 'skilltriks-lms' ),
		4  => __( 'Course updated.', 'skilltriks-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Course restored to revision from %s', 'skilltriks-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Course published. <a href="%s">View Course</a>', 'skilltriks-lms' ), esc_url( $permalink ) ),
		7  => __( 'Course saved.', 'skilltriks-lms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Course submitted. <a target="_blank" href="%s">Preview Course</a>', 'skilltriks-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Course scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Course</a>', 'skilltriks-lms' ), date_i18n( __( 'M j, Y @ G:i', 'skilltriks-lms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Course draft updated. <a target="_blank" href="%s">Preview Course</a>', 'skilltriks-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_course_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_course` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_course` post type.
 */
function stlms_course_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_COURSE_CPT ] = array(
		/* translators: %s: Number of Courses. */
		'updated'   => _n( '%s Course updated.', '%s Courses updated.', $bulk_counts['updated'], 'skilltriks-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Course not updated, somebody is editing it.', 'skilltriks-lms' ) :
						/* translators: %s: Number of Courses. */
						_n( '%s Course not updated, somebody is editing it.', '%s Courses not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks-lms' ),
		/* translators: %s: Number of Courses. */
		'deleted'   => _n( '%s Course permanently deleted.', '%s Courses permanently deleted.', $bulk_counts['deleted'], 'skilltriks-lms' ),
		/* translators: %s: Number of Courses. */
		'trashed'   => _n( '%s Course moved to the Trash.', '%s Courses moved to the Trash.', $bulk_counts['trashed'], 'skilltriks-lms' ),
		/* translators: %s: Number of Courses. */
		'untrashed' => _n( '%s Course restored from the Trash.', '%s Courses restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_course_bulk_updated_messages', 10, 2 );
