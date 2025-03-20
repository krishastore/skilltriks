<?php
/**
 * Template: Lesson Settings Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$duration      = $settings['duration'];
$duration_type = $settings['duration_type'];
?>

<div class="stlms-lesson-duration">
	<label><?php esc_html_e( 'Duration', 'skilltriks-lms' ); ?></label>
	<div class="stlms-duration-input">
		<input type="number" value="<?php echo (int) $duration; ?>" step="1" min="0" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration]">
		<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
			<option value="minute"<?php selected( 'minute', $duration_type ); ?>><?php esc_html_e( 'Minute(s)', 'skilltriks-lms' ); ?></option>
			<option value="hour"<?php selected( 'hour', $duration_type ); ?>><?php esc_html_e( 'Hour(s)', 'skilltriks-lms' ); ?></option>
			<option value="day"<?php selected( 'day', $duration_type ); ?>><?php esc_html_e( 'Day(s)', 'skilltriks-lms' ); ?></option>
			<option value="week"<?php selected( 'week', $duration_type ); ?>><?php esc_html_e( 'Week(s)', 'skilltriks-lms' ); ?></option>
		</select>
	</div>
</div>
<?php do_action( 'stlms_lesson_before_material_box', $settings, $post_id, $this ); ?>
<div class="stlms-materials-box brd-0">
	<div class="stlms-materials-box__header">
		<h3><?php esc_html_e( 'Materials', 'skilltriks-lms' ); ?></h3>
		<p><?php echo esc_html( sprintf( __( 'Max Size: %s   |   Format: .PDF, .TXT', 'skilltriks-lms' ), esc_html( size_format( $max_upload_size ) ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></p>
	</div>
</div>
<div class="stlms-materials-box">
	<div class="stlms-materials-box__body">
		<div class="stlms-materials-list">
			<ul>
				<li><strong><?php esc_html_e( 'File Title', 'skilltriks-lms' ); ?></strong></li>
				<li><strong><?php esc_html_e( 'Method', 'skilltriks-lms' ); ?></strong></li>
				<li><strong><?php esc_html_e( 'Action', 'skilltriks-lms' ); ?></strong></li>
			</ul>
			<?php
				require_once STLMS_TEMPLATEPATH . '/admin/lesson/materials-item.php';
			?>
		</div>
	</div>	
	<div class="stlms-materials-box__footer">
		<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'skilltriks-lms' ); ?></button>
	</div>
</div>
<?php do_action( 'stlms_lesson_after_material_box', $settings, $post_id, $this ); ?>

<script id="materials_item_tmpl" type="text/template">
	<div class="stlms-materials-list-item material-add-new">
		<ul class="hidden">
			<li class="assignment-title"></li>
			<li class="assignment-type"><?php esc_html_e( 'Upload', 'skilltriks-lms' ); ?></li>
			<li>
				<div class="stlms-materials-list-action">
					<a href="javascript:;" class="edit-material">
						<svg class="icon" width="12" height="12">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
						</svg>
						<?php esc_html_e( 'Edit', 'skilltriks-lms' ); ?>
					</a>
					<a href="javascript:;" class="stlms-delete-link">
						<svg class="icon" width="12" height="12">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
						</svg>
						<?php esc_html_e( 'Remove', 'skilltriks-lms' ); ?>
					</a>
				</div>
			</li>
		</ul>
		<div class="stlms-materials-item">
			<div class="stlms-media-choose">
				<label><?php esc_html_e( 'File Title', 'skilltriks-lms' ); ?></label>
				<input type="text" class="material-file-title" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][title]" placeholder="<?php esc_attr_e( 'Enter File Title', 'skilltriks-lms' ); ?>">
			</div>
			<div class="stlms-media-choose material-type">
				<label><?php esc_html_e( 'Method', 'skilltriks-lms' ); ?></label>
				<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][method]">
					<option value="upload"><?php esc_html_e( 'Upload', 'skilltriks-lms' ); ?></option>
					<option value="external"><?php esc_html_e( 'External', 'skilltriks-lms' ); ?></option>
				</select>
			</div>
			<div class="stlms-media-choose" data-media_type="choose_file">
				<label><?php esc_html_e( 'Choose File', 'skilltriks-lms' ); ?></label>
				<div class="stlms-media-file">
					<a href="javascript:;" class="stlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_material_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Choose File', 'skilltriks-lms' ); ?></a>
					<span class="stlms-media-name"><?php esc_html_e( 'No File Chosen', 'skilltriks-lms' ); ?></span>
					<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][media_id]">
				</div>
			</div>
			<div class="stlms-media-choose hidden" data-media_type="file_url">
				<label><?php esc_html_e( 'File URL', 'skilltriks-lms' ); ?></label>
				<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][external_url]" placeholder="<?php esc_attr_e( 'Enter File URL', 'skilltriks-lms' ); ?>">
			</div>
			<?php
			do_action(
				'stlms_lesson_material_item',
				array(
					'method'       => 'upload',
					'title'        => '',
					'media_id'     => 0,
					'external_url' => '',
				),
				$this
			);
			?>
			<div class="stlms-media-choose">
				<button type="button" class="button button-primary stlms-save-material">
					<?php esc_html_e( 'Done', 'skilltriks-lms' ); ?>
				</button>
				<button type="button" class="stlms-remove-material">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					<?php esc_html_e( 'Delete', 'skilltriks-lms' ); ?>
				</button>
			</div>
		</div>
	</div>
</script>