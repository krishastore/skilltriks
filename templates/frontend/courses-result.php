<?php
/**
 * Template: Course Final Result Page
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

load_template(
	\ST\Lms\locate_template( 'course-result.php' ),
	true
);

get_footer();
