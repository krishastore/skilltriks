<?php
/**
 * Template: Course Details Page
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$course_id = get_the_ID();

/**
 * Before course content action.
 *
 * @param int $course_id Course ID
 */
do_action( 'stlms_before_single_course', $course_id );

global $stlms_course_data;
load_template(
	\ST\Lms\locate_template( 'course-detail.php' ),
	true,
	array(
		'course_id'   => $course_id,
		'course_data' => $stlms_course_data,
	)
);

/**
 * After course content action.
 *
 * @param int $course_id Course ID
 */
do_action( 'stlms_after_single_course', $course_id );

get_footer();
