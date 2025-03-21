<?php
/**
 * Template: Quiz Setting Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="stlms-quiz-settings">
	<?php do_action( 'stlms_quiz_settings_fields_before', $settings, $post_id, $this ); ?>
	<ul>
		<li>
			<div class="stlms-setting-label"><?php esc_html_e( 'Duration', 'skilltriks-lms' ); ?></div>
			<div class="stlms-setting-option">
				<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration]" type="number" class="stlms-setting-number-input" step="1" min="0" value="<?php echo (int) $settings['duration']; ?>">
				<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
					<option value="minute"<?php selected( 'minute', $settings['duration_type'] ); ?>><?php esc_html_e( 'Minute(s)', 'skilltriks-lms' ); ?></option>
					<option value="hour"<?php selected( 'hour', $settings['duration_type'] ); ?>><?php esc_html_e( 'Hour(s)', 'skilltriks-lms' ); ?></option>
					<option value="day"<?php selected( 'day', $settings['duration_type'] ); ?>><?php esc_html_e( 'Day(s)', 'skilltriks-lms' ); ?></option>
					<option value="week"<?php selected( 'week', $settings['duration_type'] ); ?>><?php esc_html_e( 'Week(s)', 'skilltriks-lms' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<div class="stlms-setting-label"><?php esc_html_e( 'Passing Marks', 'skilltriks-lms' ); ?></div>
			<div class="stlms-setting-option">
				<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][passing_marks]" type="number" class="stlms-setting-number-input" step="1" min="1" value="<?php echo (int) $settings['passing_marks']; ?>">
			</div>
		</li>
		<li>
			<div class="stlms-setting-label"><?php esc_html_e( 'Negative Marking', 'skilltriks-lms' ); ?></div>
			<div class="stlms-setting-option">
				<div class="stlms-setting-checkbox">
					<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][negative_marking]" type="checkbox" id="stlms-neg-mark"<?php checked( 1, $settings['negative_marking'] ); ?>>
					<label for="stlms-neg-mark"><?php esc_html_e( 'Each question that answer wrongly, the total point is deducted exactly from the question\'s point.', 'skilltriks-lms' ); ?></label>
				</div>
			</div>
		</li>
		<li>
			<div class="stlms-setting-label"><?php esc_html_e( 'Review', 'skilltriks-lms' ); ?></div>
			<div class="stlms-setting-option">
				<div class="stlms-setting-checkbox">
					<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][review]" type="checkbox" id="stlms-review"<?php checked( 1, $settings['review'] ); ?>>
					<label for="stlms-review"><?php esc_html_e( 'Allow students to review this quiz after they finish the quiz.', 'skilltriks-lms' ); ?></label>
				</div>
			</div>
		</li>
		<li>
			<div class="stlms-setting-label"><?php esc_html_e( 'Show Correct Answer', 'skilltriks-lms' ); ?></div>
			<div class="stlms-setting-option">
				<div class="stlms-setting-checkbox">
					<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][show_correct_review]" type="checkbox" id="stlms-show-ans"<?php checked( 1, $settings['show_correct_review'] ); ?>>
					<label for="stlms-show-ans">
						<?php
							esc_html_e( 'Allow students to view the correct answer to the question in reviewing this quiz.', 'skilltriks-lms' );
						?>
					</label>
				</div>
			</div>
		</li>
		<?php do_action( 'stlms_quiz_setting_field', $settings, $post_id, $this ); ?>
	</ul>
	<?php do_action( 'stlms_quiz_settings_fields_after', $settings, $post_id, $this ); ?>
</div>
