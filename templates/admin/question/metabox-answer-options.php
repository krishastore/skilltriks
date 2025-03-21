<?php
/**
 * Template: Answer Options Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php wp_nonce_field( STLMS_BASEFILE, 'stlms_nonce', false ); ?>
<div class="stlms-answer-wrap">
	<div class="stlms-answer-type">
		<label for="answers_field">
			<?php esc_html_e( 'Select Answer Type', 'skilltriks-lms' ); ?>
		</label>
		<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[type]" id="stlms_answer_type">
			<option value="true_or_false"<?php selected( 'true_or_false', $type ); ?>><?php esc_html_e( 'True Or False ', 'skilltriks-lms' ); ?></option>
			<option value="multi_choice"<?php selected( 'multi_choice', $type ); ?>><?php esc_html_e( 'Multi Choice ', 'skilltriks-lms' ); ?></option>
			<option value="single_choice"<?php selected( 'single_choice', $type ); ?>><?php esc_html_e( 'Single Choice ', 'skilltriks-lms' ); ?></option>
			<option value="fill_blank"<?php selected( 'fill_blank', $type ); ?>><?php esc_html_e( 'Fill In Blanks ', 'skilltriks-lms' ); ?></option>
		</select>
	</div>

	<div class="stlms-answer-group<?php echo 'true_or_false' !== $type ? ' hidden' : ''; ?>" id="true_or_false">
		<?php
			$corret_answers = isset( $data['true_or_false_answers'] ) ? $data['true_or_false_answers'] : '';
			$answers        = isset( $data['true_or_false'] ) ? $data['true_or_false'] :
			array(
				0 => __( 'True', 'skilltriks-lms' ),
				1 => __( 'False', 'skilltriks-lms' ),
			);
			?>
			<div class="stlms-options-table">
				<div class="stlms-options-table__header">
					<ul class="stlms-options-table__list">
						<li><?php esc_html_e( 'Options ', 'skilltriks-lms' ); ?></li>
						<li class="stlms-option-check-td"><?php esc_html_e( 'Correct Option', 'skilltriks-lms' ); ?></li>
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
										<input type="text" class="stlms-option-value-input" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[true_or_false][]" readonly>
									</div>
								</li>
								<li class="stlms-option-check-td">
									<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[true_or_false_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
								</li>
							</ul>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
	</div>

	<div class="stlms-answer-group <?php echo 'multi_choice' !== $type ? ' hidden' : ''; ?>" id="multi_choice">
		<?php
			$corret_answers = ! empty( $data['multi_choice_answers'] ) ? $data['multi_choice_answers'] : array();
			$answers        = isset( $data['multi_choice'] ) ? $data['multi_choice'] : array_fill( 0, 4, '' );
		?>
			<div class="stlms-options-table">
				<div class="stlms-options-table__header">
					<ul class="stlms-options-table__list">
						<li><?php esc_html_e( 'Options', 'skilltriks-lms' ); ?></li>
						<li class="stlms-option-check-td"><?php esc_html_e( 'Correct Option', 'skilltriks-lms' ); ?></li>
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
										<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[multi_choice][]">
									</div>
								</li>
								<li class="stlms-option-check-td">
									<input type="checkbox" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[multi_choice_answers][]"<?php echo in_array( wp_hash( $answer ), $corret_answers, true ) ? ' checked' : ''; ?>>
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

	<div class="stlms-answer-group <?php echo 'single_choice' !== $type ? ' hidden' : ''; ?>" id="single_choice">
		<?php
			$corret_answers = ! empty( $data['single_choice_answers'] ) ? $data['single_choice_answers'] : '';
			$answers        = isset( $data['single_choice'] ) ? $data['single_choice'] : array_fill( 0, 4, '' );
		?>
			<div class="stlms-options-table">
				<div class="stlms-options-table__header">
					<ul class="stlms-options-table__list">
						<li><?php esc_html_e( 'Options', 'skilltriks-lms' ); ?></li>
						<li class="stlms-option-check-td"><?php esc_html_e( 'Correct Option', 'skilltriks-lms' ); ?></li>
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
										<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[single_choice][]">
									</div>
								</li>
								<li class="stlms-option-check-td">
									<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[single_choice_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
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

	<div class="stlms-answer-group <?php echo 'fill_blank' !== $type ? ' hidden' : ''; ?>" id="fill_blank">
		<?php
			$mandatory_answers = isset( $data['mandatory_answers'] ) ? $data['mandatory_answers'] : '';
			$optional_answers  = ! empty( $data['optional_answers'] ) ? $data['optional_answers'] : array_fill( 0, 4, '' );
		?>
		<div class="stlms-add-accepted-answers">
			<h3><?php esc_html_e( 'Add Accepted Answers', 'skilltriks-lms' ); ?></h3>
			<ul>
				<li>
					<label><?php esc_html_e( 'Mandatory', 'skilltriks-lms' ); ?></label>
					<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[mandatory_answers]" value="<?php echo esc_attr( $mandatory_answers ); ?>">
				</li>
				<?php foreach ( $optional_answers as $optional_answer ) : ?>
					<li>
						<label><?php esc_html_e( 'Optional', 'skilltriks-lms' ); ?></label>
						<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[optional_answers][]" value="<?php echo esc_attr( $optional_answer ); ?>">
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="stlms-add-option hidden">
		<button type="button" class="button stlms-add-answer"><?php esc_html_e( 'Add More Options', 'skilltriks-lms' ); ?></button>
	</div>
</div>
