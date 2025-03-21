<?php
/**
 * Template: Curriculum Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php wp_nonce_field( STLMS_BASEFILE, 'stlms_nonce', false ); ?>
<div class="stlms-quiz-qus-wrap">
	<div class="stlms-snackbar-notice"><p></p></div>
	<?php do_action( 'stlms_course_curriculum_before', $this->curriculums, $this ); ?>
	<ul class="stlms-quiz-qus-list">
		<?php
		foreach ( $this->curriculums as $key => $curriculum ) :
			$items      = isset( $curriculum['items'] ) ? $curriculum['items'] : array();
			$item_types = array_map( 'get_post_type', $items );
			$item_types = array_count_values( $item_types );
			$items      = array_merge( array_filter( $items ), array( '' ) );
			?>
			<li>
				<div class="stlms-quiz-qus-item">
					<div class="stlms-quiz-qus-item__header">
						<div class="stlms-options-drag">
							<svg class="icon" width="8" height="13">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
							</svg>
						</div>
						<div class="stlms-quiz-qus-name">
							<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[curriculum][<?php echo (int) $key; ?>][section_name]" placeholder="<?php esc_attr_e( 'Create New Section Name', 'skilltriks-lms' ); ?>" value="<?php echo isset( $curriculum['section_name'] ) ? esc_attr( $curriculum['section_name'] ) : ''; ?>">
							<div class="stlms-quiz-qus-point">
								<ul>
									<li class="lesson-count">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span><?php echo isset( $item_types[ \ST\Lms\STLMS_LESSON_CPT ] ) ? (int) $item_types[ \ST\Lms\STLMS_LESSON_CPT ] : 0; ?></span>
									</li>
									<li class="quiz-count">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span><?php echo isset( $item_types[ \ST\Lms\STLMS_QUIZ_CPT ] ) ? (int) $item_types[ \ST\Lms\STLMS_QUIZ_CPT ] : 0; ?></span>
									</li>
								</ul>
							</div>
						</div>
						<div class="stlms-quiz-qus-toggle" data-accordion="true">
							<svg class="icon" width="18" height="18">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
							</svg>
						</div>
					</div>
					<div class="stlms-quiz-qus-item__body">
						<div class="stlms-curriculum-desc">
							<textarea name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[curriculum][<?php echo (int) $key; ?>][section_desc]" placeholder="<?php esc_attr_e( 'Section description..', 'skilltriks-lms' ); ?>"><?php echo isset( $curriculum['section_desc'] ) ? esc_textarea( $curriculum['section_desc'] ) : ''; ?></textarea>
						</div>
						<?php do_action( 'stlms_course_curriculum_section_field', $key, $this ); ?>
						<div class="stlms-curriculum-item-list">
							<?php
							foreach ( $items as $attached_id ) :
								$item_id        = $attached_id;
								$item_title     = $attached_id ? get_the_title( $attached_id ) : '';
								$item_post_type = $attached_id ? get_post_type( $attached_id ) : '';
								$item_type      = 'lesson';
								if ( \ST\Lms\STLMS_QUIZ_CPT === $item_post_type ) {
									$item_type = 'quiz';
								}
								?>
								<div class="stlms-curriculum-item">
									<div class="stlms-curriculum-item-drag">
										<svg class="icon drag-icon<?php echo '' === $attached_id ? ' hidden' : ''; ?>" width="8" height="13">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
										<?php if ( '' === $attached_id ) : ?>
											<svg class="icon plus-icon" width="8" height="13">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#plus-icon"></use>
											</svg>
										<?php endif; ?>
									</div>
									<div class="stlms-curriculum-dd">
										<button class="stlms-curriculum-dd-button">
											<svg class="icon lesson-icon<?php echo 'quiz' === $item_type ? ' hidden' : ''; ?>" width="16" height="16">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
											</svg>
											<svg class="icon quiz-icon<?php echo 'lesson' === $item_type ? ' hidden' : ''; ?>" width="16" height="16">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
											</svg>
											<?php if ( '' === $attached_id ) : ?>
												<svg class="icon down-arrow-icon" width="18" height="18">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
												</svg>
											<?php endif; ?>
										</button>
										<?php if ( '' === $attached_id ) : ?>
											<ul class="stlms-curriculum-type">									
												<li class="active" data-type="lesson">
													<svg class="icon" width="16" height="16">
														<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
													</svg>
													<span><?php esc_html_e( 'Lesson', 'skilltriks-lms' ); ?></span>
												</li>
												<li data-type="quiz">
													<svg class="icon" width="16" height="16">
														<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
													</svg>
													<span><?php esc_html_e( 'Quiz', 'skilltriks-lms' ); ?></span>
												</li>
											</ul>
										<?php endif; ?>
									</div>
									<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[curriculum][<?php echo (int) $key; ?>][items][]" value="<?php echo (int) $item_id; ?>">
									<input type="text" class="stlms-curriculum-item-name" placeholder="<?php esc_attr_e( 'Add A New Item', 'skilltriks-lms' ); ?>" value="<?php echo ! empty( $item_title ) ? esc_attr( $item_title ) : ''; ?>"<?php echo '' !== $attached_id ? ' readonly' : ''; ?>>
									<div class="stlms-curriculum-item-action<?php echo empty( $attached_id ) ? ' hidden' : ''; ?>">
										<?php if ( ! empty( $item_id ) && \is_post_type_viewable( get_post_type( $item_id ) ) ) : ?>
											<a href="<?php echo esc_url( get_the_permalink( $item_id ) ); ?>" class="curriculum-view-item" target="_blank">
												<svg class="icon" width="12" height="12">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
												</svg>
											</a>
										<?php endif; ?>
										<a href="<?php echo esc_url( get_edit_post_link( $item_id, null ) ); ?>" class="curriculum-edit-item" target="_blank">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
											</svg>
										</a>
										<a href="javascript:;" class="curriculum-remove-item">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>
										</a>
									</div>
								</div>
							<?php endforeach ?>
						</div>
						<div class="stlms-quiz-qus-item__footer">
							<a href="javascript:;" class="button select-items"><?php esc_html_e( 'Select Items', 'skilltriks-lms' ); ?></a>
							<a href="javascript:;" class="stlms-delete-link">
								<svg class="icon" width="12" height="12">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
								</svg>
								<?php esc_html_e( 'Delete', 'skilltriks-lms' ); ?>
							</a>
						</div>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="stlms-quiz-qus-footer">
		<a href="javascript:;" class="button button-primary add-new-section"><?php esc_html_e( 'Add New Section', 'skilltriks-lms' ); ?></a>
	</div>
	<?php do_action( 'stlms_course_curriculum_after', $this->curriculums, $this ); ?>
</div>
<?php
require_once STLMS_TEMPLATEPATH . '/admin/course/modal-popup.php';

