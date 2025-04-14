<?php
/**
 * Template: Popup html template.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="course_list_modal" class="hidden" style="max-width:463px">
	<div class="stlms-qus-bank-modal">
		<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for the course', 'skilltriks' ); ?>" class="stlms-qus-bank-search">
		<div class="stlms-qus-list" id="stlms_course_list">
			<?php
			if ( ! empty( $fetch_request ) ) :
					$lesson_id = isset( $lesson_id ) ? $lesson_id : 0;
					$args      = array(
						'posts_per_page' => -1,
						'post_type'      => \ST\Lms\STLMS_COURSE_CPT,
						'post_status'    => 'publish',
					);
					$courses   = get_posts( $args );
					?>
				<?php if ( ! empty( $courses ) ) : ?>
					<ul class="stlms-qus-list-scroll">
						<?php
						foreach ( $courses as $key => $course ) :
							?>
							<li>
								<div class="stlms-setting-checkbox">
									<input type="checkbox" class="stlms-choose-course" id="stlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $course->ID; ?>">
									<label for="stlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $course->post_title ); ?></label>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php esc_html_e( 'No course found.', 'skilltriks' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<span class="spinner is-active"></span>
			<?php endif; ?>
		</div>

		<div class="stlms-qus-bank-add">
			<button class="button button-primary stlms-add-course" disabled><?php esc_html_e( 'Save', 'skilltriks' ); ?></button>
			<span class="stlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'skilltriks' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
		<p class="stlms-notice"><?php esc_html_e( 'Note: It will be added in the last curriculum section.', 'skilltriks' ); ?></p>
	</div>
</div>
