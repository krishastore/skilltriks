<?php
/**
 * Template: Course Curriculum - File.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$file_url = '';
if ( ! empty( $args['curriculum']['media']['file_id'] ) ) {
	$file_url = wp_get_attachment_url( $args['curriculum']['media']['file_id'] );
} elseif ( ! empty( $args['curriculum']['media']['file_url'] ) ) {
	$file_url = $args['curriculum']['media']['file_url'];
}
?>
<div class="stlms-lesson-view__body">
	<div class="stlms-lesson-video-box stlms-pdf-view">
		<iframe src="<?php echo esc_url( $file_url ); ?>" frameborder="0"></iframe>
	</div>
</div>
