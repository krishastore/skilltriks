<?php
/**
 * Template: Course detail page content.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$curriculums_list   = ! empty( $args['course_data']['curriculums'] ) ? $args['course_data']['curriculums'] : array();
$current_curriculum = ! empty( $args['course_data']['current_curriculum'] ) ? $args['course_data']['current_curriculum'] : array();
$content_type       = isset( $current_curriculum['media']['media_type'] ) ? $current_curriculum['media']['media_type'] : 'quiz';

$section_id      = get_query_var( 'section' ) ? (int) get_query_var( 'section' ) : 1;
$current_item_id = get_query_var( 'item_id' ) ? (int) get_query_var( 'item_id' ) : 0;

load_template(
	\ST\Lms\locate_template( "course-content-$content_type.php" ),
	true,
	array(
		'course_id'  => $args['course_id'],
		'curriculum' => $current_curriculum,
	)
);
?>

<?php if ( ! empty( $curriculums_list ) ) : ?>
<div class="stlms-lesson-sidebar">
	<div class="stlms-lesson-toggle">
		<svg class="icon" width="20" height="20">
			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#menu-burger"></use>
		</svg>
		<span><?php esc_html_e( 'Course Content', 'skilltriks' ); ?></span>
		<svg class="icon-cross" width="20" height="20">
			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
		</svg>
	</div>
	<div class="stlms-lesson-accordion">
		<div class="stlms-accordion">
			<?php
			$inactive = false;
			foreach ( $curriculums_list as $item_key => $curriculums ) :
				$items          = ! empty( $curriculums['items'] ) ? $curriculums['items'] : array();
				$total_duration = \ST\Lms\count_duration( $items );
				$duration_str   = \ST\Lms\seconds_to_hours_str( $total_duration );
				?>
				<div class="stlms-accordion-item" data-expanded="true">
					<div class="stlms-accordion-header active">
						<div class="stlms-lesson-title">
							<div class="no"><?php echo esc_html( ++$item_key ); ?>.</div>
							<div class="stlms-lesson-name">
								<div class="name"><?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
								<div class="info">
									<span><?php echo esc_html( sprintf( '%d/%d', 1, count( $curriculums['items'] ) ) ); ?></span>
									<?php if ( ! empty( $duration_str ) ) : ?>
										<span><?php echo esc_html( $duration_str ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="stlms-accordion-collapse" style="display: block;">
						<div class="stlms-lesson-list">
							<ul>
								<?php
								foreach ( $items as $key => $item ) :
									++$key;
									$media_type      = 'quiz-2';
									$item_id         = isset( $item['item_id'] ) ? $item['item_id'] : 0;
									$curriculum_type = 'quiz_id';
									if ( \ST\Lms\STLMS_LESSON_CPT === get_post_type( $item_id ) ) {
										$media           = get_post_meta( $item_id, \ST\Lms\META_KEY_LESSON_MEDIA, true );
										$media_type      = ! empty( $media['media_type'] ) ? $media['media_type'] : '';
										$media_type      = 'text' === $media_type ? 'file-text' : $media_type;
										$settings        = get_post_meta( $item_id, \ST\Lms\META_KEY_LESSON_SETTINGS, true );
										$curriculum_type = 'lesson_id';
									} else {
										$settings = get_post_meta( $item_id, \ST\Lms\META_KEY_QUIZ_SETTINGS, true );
									}
									$duration      = isset( $settings['duration'] ) ? (int) $settings['duration'] : '';
									$duration_type = isset( $settings['duration_type'] ) ? $settings['duration_type'] : '';
									if ( empty( $current_item_id ) && $key > 1 ) {
										$inactive = true;
									}
									if ( $section_id === $item_key && $current_item_id === $item_id ) {
										$inactive = true;
									}
									$course_link = get_permalink() . $item_key . '/' . rtrim( $curriculum_type, '_id' ) . '/' . $item_id;
									?>
								<li class="<?php echo $current_item_id === $item_id ? esc_attr( 'active' ) : ''; ?>">
									<label>
										<?php if ( $section_id === $item_key && ( $current_item_id === $item_id ) ) : ?>
											<input type="checkbox" name="<?php echo esc_attr( $curriculum_type ); ?>[]" class="stlms-check curriculum-progress-box" value="<?php echo esc_attr( $item_id ); ?>" disabled>
										<?php else : ?>
											<input type="checkbox" name="<?php echo esc_attr( $curriculum_type ); ?>[]" value="<?php echo esc_attr( $item_id ); ?>" class="stlms-check curriculum-progress-box"<?php echo $inactive ? ' readonly' : ''; ?><?php checked( true, ! $inactive ); ?> disabled>
										<?php endif; ?>
										<span class="stlms-lesson-class" data-key="<?php echo esc_html( wp_hash( $course_link ) ); ?>">
											<span class="class-name"><span><?php echo esc_html( sprintf( '%s.%s.', $item_key, $key ) ); ?></span> <?php echo esc_html( get_the_title( $item_id ) ); ?></span>
											<span class="class-type">
												<svg class="icon" width="16" height="16">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#<?php echo esc_html( $media_type ); ?>">
													</use>
												</svg>
												<?php
												if ( ! empty( $duration ) ) {
													$duration_type .= $duration > 1 ? 's' : '';
													echo esc_html( sprintf( '%d %s', $duration, ucfirst( $duration_type ) ) );
												} else {
													echo esc_html__( 'No duration', 'skilltriks' );
												}
												?>
											</span>
										</span>
									</label>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="stlms-accordion-item" data-expanded="true">
				<div class="stlms-accordion-header no-accordion">
					<div class="stlms-lesson-title">
						<div class="no"><?php echo esc_html( ++$item_key ); ?>.</div>
						<div class="stlms-lesson-name">
							<div class="name"><?php esc_html_e( 'Conclusion', 'skilltriks' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<?php
endif;
