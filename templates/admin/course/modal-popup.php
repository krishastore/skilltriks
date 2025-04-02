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

<div id="select_items" class="hidden" style="max-width:463px">
	<div class="stlms-qus-bank-modal">
		<div class="stlms-tab-container">
			<div class="stlms-tabs-nav">
				<button class="stlms-tab active" data-tab="assign-quiz-list" data-filter_type="<?php echo esc_attr( \ST\Lms\STLMS_LESSON_CPT ); ?>"><?php esc_html_e( 'Lesson', 'skilltriks' ); ?></button>
				<button class="stlms-tab" data-tab="assign-quiz-list" data-filter_type="<?php echo esc_attr( \ST\Lms\STLMS_QUIZ_CPT ); ?>"><?php esc_html_e( 'Quiz', 'skilltriks' ); ?></button>
			</div>

			<div class="stlms-tab-content active" data-tab="assign-quiz-list">
				<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for items', 'skilltriks' ); ?>" class="stlms-qus-bank-search">
				<div class="stlms-qus-list" id="curriculums_list">
				<?php
				if ( ! empty( $fetch_request ) ) :
					$args  = array(
						'posts_per_page' => -1,
						'post_type'      => $type,
						'post_status'    => 'publish',
					);
					$items = get_posts( $args );
					?>
					<?php if ( ! empty( $items ) ) : ?>
					<ul class="stlms-qus-list-scroll">
						<?php
						foreach ( $items as $key => $item ) :
							$disabled_item = in_array( (int) $item->ID, $existing_items, true );
							?>
						<li class="<?php echo $disabled_item ? 'disabled-choose-item' : ''; ?>">
							<div class="stlms-setting-checkbox">
								<input type="checkbox" class="stlms-choose-item" id="stlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $item->ID; ?>"<?php checked( true, $disabled_item, true ); ?>>
								<label for="stlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $item->post_title ); ?></label>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
						<p><?php esc_html_e( 'No items found.', 'skilltriks' ); ?></p>
					<?php endif; ?>
				<?php else : ?>
					<span class="spinner is-active"></span>
			<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="stlms-qus-bank-add">
			<button class="button button-primary stlms-add-item" disabled><?php esc_html_e( 'Add', 'skilltriks' ); ?></button>
			<span
				class="stlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'skilltriks' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>
