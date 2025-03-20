<?php
/**
 * Template: Course Curriculum - Text.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = isset( $args['curriculum']['media']['text'] ) ? $args['curriculum']['media']['text'] : '';
?>
<div class="stlms-lesson-view__body">
	<div class="stlms-quiz-view">
		<div class="stlms-quiz-content">
			<?php
				echo wp_kses_post( $content );
			?>
		</div>
	</div>
</div>
