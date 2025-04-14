<?php
/**
 * Template: Question Settings.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="stlms-qus-setting-wrap">
	<?php do_action( 'stlms_question_setting_fields_before', $settings, $post_id, $this ); ?>
	<div class="stlms-qus-setting-header">
		<div>
			<label for="points_field">
				<?php esc_html_e( 'Marks/Points: ', 'skilltriks' ); ?>
			</label>
			<input type="number" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][points]"
				value="<?php echo isset( $settings['points'] ) ? (int) $settings['points'] : 1; ?>" step="1" min="0">
		</div>
		<div>
			<label for="levels_field">
				<?php esc_html_e( 'Difficulty Level', 'skilltriks' ); ?>
			</label>
			<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][levels]">
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
			<label><input type="checkbox" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][status]" value="1"<?php checked( $status, 1 ); ?>><?php esc_html_e( 'Hide Question? ', 'skilltriks' ); ?></label>
		</div>
	</div>
	<div class="stlms-qus-setting-body">
		<h3><?php esc_html_e( 'Show Feedback/Hint ', 'skilltriks' ); ?></h3>

		<div class="stlms-hint-box">
			<label for="hint_field">
				<?php esc_html_e( 'Correctly Answered Feedback: ', 'skilltriks' ); ?>
				<div class="stlms-tooltip">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#help"></use>
					</svg>
					<span class="stlms-tooltiptext">
						<?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'skilltriks' ); ?>
					</span>
				</div>
			</label>
			<textarea
				name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][hint]"><?php echo isset( $settings['hint'] ) ? esc_textarea( $settings['hint'] ) : ''; ?></textarea>
		</div>
		<div class="stlms-hint-box">
			<label for="explanation_field" style="color: #B20000;">
				<?php esc_html_e( 'Incorrectly Answered Feedback: ', 'skilltriks' ); ?>
				<div class="stlms-tooltip">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#help"></use>
					</svg>
					<span class="stlms-tooltiptext">
						<?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'skilltriks' ); ?>
					</span>
				</div>
			</label>
			<textarea
				name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][explanation]"><?php echo isset( $settings['explanation'] ) ? esc_textarea( $settings['explanation'] ) : ''; ?></textarea>
		</div>
	</div>
	<?php do_action( 'stlms_question_setting_fields_after', $settings, $post_id, $this ); ?>
</div>
