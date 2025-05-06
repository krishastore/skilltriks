<?php
/**
 * Template: Question list.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php
foreach ( $questions as $question_id ) :
	$question_title = get_the_title( $question_id );
	$qtype          = get_post_meta( $question_id, \ST\Lms\META_KEY_QUESTION_TYPE, true );
	$data           = \ST\Lms\get_question_by_type( $question_id, $qtype );
	$qtype          = ! empty( $qtype ) ? $qtype : 'true_or_false';

	// Get question settings.
	$settings    = get_post_meta( $question_id, \ST\Lms\META_KEY_QUESTION_SETTINGS, true );
	$settings    = ! empty( $settings ) ? $settings : array();
	$point       = isset( $settings['points'] ) ? (int) $settings['points'] : 0;
	$hint        = isset( $settings['hint'] ) ? esc_textarea( $settings['hint'] ) : '';
	$explanation = isset( $settings['explanation'] ) ? esc_textarea( $settings['explanation'] ) : '';
	$levels      = isset( $settings['levels'] ) ? esc_textarea( $settings['levels'] ) : '';
	$qstatus     = isset( $settings['status'] ) ? $settings['status'] : 0;
	$is_draft    = 'draft' === get_post_status( $question_id );
	?>
	<li>
		<input type="hidden" class="stlms-qid" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[question_id][]" value="<?php echo (int) $question_id; ?>">
		<div class="stlms-quiz-qus-item">
			<div class="stlms-quiz-qus-item__header">
				<div class="stlms-options-drag">
					<svg class="icon" width="8" height="13">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
					</svg>
				</div>
				<div class="stlms-quiz-qus-name">
					<span><?php echo esc_html( $question_title ); ?><?php $is_draft ? esc_html_e( ' - Draft', 'skilltriks' ) : ''; ?></span>
					<span class="stlms-quiz-qus-point"><?php echo esc_html( sprintf( _n( '%s Point', '%s Points', $point, 'skilltriks' ), number_format_i18n( $point ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
				</div>
				<div class="stlms-quiz-qus-toggle" data-accordion="true">
					<svg class="icon" width="18" height="18">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
					</svg>
				</div>
			</div>
			<div class="stlms-quiz-qus-item__body">
				<div class="stlms-answer-wrap">
					<div class="stlms-quiz-name">
						<input type="text" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][post_title]" value="<?php echo esc_attr( $question_title ); ?>" placeholder="<?php esc_attr_e( 'Enter Your Question Name ', 'skilltriks' ); ?>">
					</div>
					<div class="stlms-answer-type">
						<label for="answers_field">
							<?php esc_html_e( 'Select Answer Type', 'skilltriks' ); ?>
						</label>
						<select name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][type]">
							<option value="true_or_false"<?php selected( 'true_or_false', $qtype ); ?>><?php esc_html_e( 'True Or False ', 'skilltriks' ); ?></option>
							<option value="multi_choice"<?php selected( 'multi_choice', $qtype ); ?>><?php esc_html_e( 'Multi Choice ', 'skilltriks' ); ?></option>
							<option value="single_choice"<?php selected( 'single_choice', $qtype ); ?>><?php esc_html_e( 'Single Choice ', 'skilltriks' ); ?></option>
							<option value="fill_blank"<?php selected( 'fill_blank', $qtype ); ?>><?php esc_html_e( 'Fill In Blanks ', 'skilltriks' ); ?></option>
						</select>
					</div>

					<div class="stlms-answer-group true_or_false<?php echo 'true_or_false' !== $qtype ? ' hidden' : ''; ?>">
						<?php
							$corret_answers = isset( $data['true_or_false_answers'] ) ? $data['true_or_false_answers'] : '';
							$answers        = isset( $data['true_or_false'] ) ? $data['true_or_false'] :
							array(
								0 => __( 'True', 'skilltriks' ),
								1 => __( 'False', 'skilltriks' ),
							);
							?>
							<div class="stlms-options-table">
								<div class="stlms-options-table__header">
									<ul class="stlms-options-table__list">
										<li><?php esc_html_e( 'Options ', 'skilltriks' ); ?></li>
										<li class="stlms-option-check-td"><?php esc_html_e( 'Correct Option', 'skilltriks' ); ?></li>
									</ul>
								</div>
								<div class="stlms-options-table__body stlms-sortable-answers">
									<div class="stlms-options-table__list-wrap">
										<?php foreach ( $answers as $key => $answer ) : ?>
											<ul class="stlms-options-table__list">
												<li>
													<div class="stlms-options-value">
														<div class="stlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<input type="text" class="stlms-option-value-input" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][true_or_false][]" readonly>
													</div>
												</li>
												<li class="stlms-option-check-td">
													<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][true_or_false_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
												</li>
											</ul>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
					</div>

					<div class="stlms-answer-group multi_choice<?php echo 'multi_choice' !== $qtype ? ' hidden' : ''; ?>">
						<?php
							$corret_answers = ! empty( $data['multi_choice_answers'] ) ? $data['multi_choice_answers'] : array();
							$answers        = isset( $data['multi_choice'] ) ? $data['multi_choice'] : array_fill( 0, 4, '' );
						?>
							<div class="stlms-options-table">
								<div class="stlms-options-table__header">
									<ul class="stlms-options-table__list">
										<li><?php esc_html_e( 'Options', 'skilltriks' ); ?></li>
										<li class="stlms-option-check-td"><?php esc_html_e( 'Correct Option', 'skilltriks' ); ?></li>
										<li class="stlms-option-action"></li>
									</ul>
								</div>
								<div class="stlms-options-table__body stlms-sortable-answers">
									<div class="stlms-options-table__list-wrap">
										<?php
										foreach ( $answers as $key => $answer ) :
											?>
											<ul class="stlms-options-table__list">
												<li>
													<div class="stlms-options-value">
														<div class="stlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="stlms-options-no"><?php echo esc_html( sprintf( '%s.', isset( $this->alphabets[ $key ] ) ? $this->alphabets[ $key ] : '' ) ); ?></div>
														<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][multi_choice][]">
													</div>
												</li>
												<li class="stlms-option-check-td">
													<input type="checkbox" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][multi_choice_answers][]"<?php echo in_array( wp_hash( $answer ), $corret_answers, true ) ? ' checked' : ''; ?>>
												</li>
												<li class="stlms-option-action">
													<button type="button" class="stlms-remove-answer">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
					</div>

					<div class="stlms-answer-group single_choice<?php echo 'single_choice' !== $qtype ? ' hidden' : ''; ?>">
						<?php
							$corret_answers = isset( $data['single_choice_answers'] ) ? $data['single_choice_answers'] : '';
							$answers        = isset( $data['single_choice'] ) ? $data['single_choice'] : array_fill( 0, 4, '' );
						?>
							<div class="stlms-options-table">
								<div class="stlms-options-table__header">
									<ul class="stlms-options-table__list">
										<li><?php esc_html_e( 'Options', 'skilltriks' ); ?></li>
										<li class="stlms-option-check-td"><?php esc_html_e( 'Correct Option', 'skilltriks' ); ?></li>
										<li class="stlms-option-action"></li>
									</ul>
								</div>
								<div class="stlms-options-table__body stlms-sortable-answers">
									<div class="stlms-options-table__list-wrap">
										<?php
										foreach ( $answers as $key => $answer ) :
											?>
											<ul class="stlms-options-table__list stlms-sortable-answers">
												<li>
													<div class="stlms-options-value">
														<div class="stlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="stlms-options-no"><?php echo esc_html( sprintf( '%s.', isset( $this->alphabets[ $key ] ) ? $this->alphabets[ $key ] : '' ) ); ?></div>
														<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][single_choice][]">
													</div>
												</li>
												<li class="stlms-option-check-td">
													<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][single_choice_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
												</li>
												<li class="stlms-option-action">
													<button type="button" class="stlms-remove-answer">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
					</div>

					<div class="stlms-answer-group fill_blank<?php echo 'fill_blank' !== $qtype ? ' hidden' : ''; ?>">
						<?php
							$mandatory_answers = isset( $data['mandatory_answers'] ) ? $data['mandatory_answers'] : '';
							$optional_answers  = ! empty( $data['optional_answers'] ) ? $data['optional_answers'] : array_fill( 0, 4, '' );
						?>
						<div class="stlms-add-accepted-answers">
							<h3><?php esc_html_e( 'Add Accepted Answers', 'skilltriks' ); ?></h3>
							<ul>
								<li>
									<label><?php esc_html_e( 'Mandatory', 'skilltriks' ); ?></label>
									<input type="text" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][mandatory_answers]" value="<?php echo esc_attr( $mandatory_answers ); ?>">
								</li>
								<?php foreach ( $optional_answers as $optional_answer ) : ?>
									<li>
										<label><?php esc_html_e( 'Optional', 'skilltriks' ); ?></label>
										<input type="text" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][optional_answers][]" value="<?php echo esc_attr( $optional_answer ); ?>">
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>

					<div class="stlms-add-option hidden">
						<button type="button"
							class="button stlms-add-answer"><?php esc_html_e( 'Add More Options', 'skilltriks' ); ?></button>
					</div>
				</div>
				<div class="stlms-qus-setting-wrap">
					<div class="stlms-answer-type">
						<label for="answers_field">
							<?php esc_html_e( 'Question Settings', 'skilltriks' ); ?>
						</label>
					</div>
					<?php do_action( 'stlms_question_setting_fields_before', $settings, $question_id, $this ); ?>
					<div class="stlms-qus-setting-header">
						<div>
							<label for="points_field">
								<?php esc_html_e( 'Marks/Points: ', 'skilltriks' ); ?>
							</label>
							<input type="number" class="stlms-question-points" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][points]" value="<?php echo isset( $settings['points'] ) ? (int) $settings['points'] : 0; ?>" step="1" min="0">
						</div>
						<div>
							<label for="levels_field">
								<?php esc_html_e( 'Difficulty Level', 'skilltriks' ); ?>
							</label>
							<select name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][levels]">
								<?php
								foreach ( \ST\Lms\question_levels() as $key => $level ) {
									?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $levels, $key ); ?>>
									<?php echo esc_html( $level ); ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div>
							<label><input type="checkbox" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][status]" value="1"<?php checked( $qstatus, 1 ); ?>><?php esc_html_e( 'Hide Question? ', 'skilltriks' ); ?> </label>
						</div>
					</div>
					<div class="stlms-qus-setting-body">
						<h3><?php esc_html_e( 'Show Feedback/Hint ', 'skilltriks' ); ?></h3>

						<div class="stlms-hint-box">
							<label for="hint_field">
								<?php esc_html_e( 'Correctly Answered Feedback: ', 'skilltriks' ); ?>
								<div class="stlms-tooltip">
									<svg class="icon" width="12" height="12">
										<use
											xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#help">
										</use>
									</svg>
									<span class="stlms-tooltiptext">
										<?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'skilltriks' ); ?>
									</span>
								</div>
							</label>
							<textarea name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][hint]"><?php echo isset( $hint ) ? esc_textarea( $hint ) : ''; ?></textarea>
						</div>
						<div class="stlms-hint-box">
							<label for="explanation_field" style="color: #B20000;">
								<?php esc_html_e( 'Incorrectly Answered Feedback: ', 'skilltriks' ); ?>
								<div class="stlms-tooltip">
									<svg class="icon" width="12" height="12">
										<use
											xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#help">
										</use>
									</svg>
									<span class="stlms-tooltiptext">
										<?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'skilltriks' ); ?>
									</span>
								</div>
							</label>
							<textarea name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][explanation]"><?php echo isset( $explanation ) ? esc_textarea( $explanation ) : ''; ?></textarea>
						</div>

						<div class="stlms-add-option">
							<button type="button" class="button button-primary stlms-save-questions" data-post_id="<?php echo (int) $question_id; ?>"><?php esc_html_e( 'Save', 'skilltriks' ); ?></button>
							<button type="button" class="button stlms-cancel-edit"><?php esc_html_e( 'Cancel', 'skilltriks' ); ?></button>
							<span class="spinner"></span>
						</div>
					</div>
					<?php do_action( 'stlms_question_setting_fields_after', $settings, $question_id, $this ); ?>
				</div>
			</div>
			<div class="stlms-quiz-qus-item__footer">
				<?php if ( ( current_user_can( 'edit_other_questions' ) && current_user_can( 'edit_published_questions' ) ) || ( current_user_can( 'manage_options' ) ) ) : // phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
				<a href="javascript:;" data-accordion="true">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
					</svg>
					<?php esc_html_e( 'Edit', 'skilltriks' ); ?>
				</a>
				<a href="javascript:;" class="stlms-duplicate-link">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#duplicate"></use>
					</svg>
					<?php esc_html_e( 'Duplicate', 'skilltriks' ); ?>
				</a>
				<?php endif; ?>
				<a href="javascript:;" class="stlms-delete-link">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					<?php esc_html_e( 'Remove', 'skilltriks' ); ?>
				</a>
			</div>
		</div>
	</li>
<?php endforeach; ?>
