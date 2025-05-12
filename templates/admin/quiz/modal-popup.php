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
<div id="add_new_question" class="hidden stlms-add-qus-modal" style="max-width:463px">
	<div class="stlms-btn-group">
		<?php if ( current_user_can( 'manage_options' ) || ( current_user_can( 'edit_published_questions' ) && current_user_can( 'edit_others_questions' ) ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
		<button class="button button-primary create-your-own"><?php esc_html_e( 'Create Your Own', 'skilltriks' ); ?></button>
		<?php endif; ?>
		<button class="button open-questions-bank"><?php esc_html_e( 'Add From Existing', 'skilltriks' ); ?></button>
		<span class="spinner"></span>
	</div>
	<p>
		<strong><?php esc_html_e( 'Tips:', 'skilltriks' ); ?></strong>
	</p>
	<p><?php esc_html_e( 'Add from existing helps you to add question from your question bank which are stored.', 'skilltriks' ); ?></p>
</div>

<div id="questions_bank" class="hidden" style="max-width:463px">
	<div class="stlms-qus-bank-modal">
		<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for the question', 'skilltriks' ); ?>" class="stlms-qus-bank-search">
		<div class="stlms-qus-list" id="stlms_qus_list">
			<?php
			if ( ! empty( $fetch_request ) ) :
				$args          = array(
					'posts_per_page' => -1,
					'post_type'      => \ST\Lms\STLMS_QUESTION_CPT,
					'post_status'    => 'publish',
				);
				$question_list = get_posts( $args );
				?>
				<?php if ( ! empty( $question_list ) ) : ?>
					<ul class="stlms-qus-list-scroll">
						<?php
						foreach ( $question_list as $key => $question ) :
							$topic = wp_get_post_terms( $question->ID, \ST\Lms\STLMS_QUESTION_TAXONOMY_TAG, array( 'fields' => 'names' ) );
							?>
							<li>
								<div class="stlms-setting-checkbox">
									<?php if ( in_array( $question->ID, $questions, true ) ) : ?>
										<input type="checkbox" class="stlms-choose-existing" id="stlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $question->ID; ?>" checked disabled>
									<?php else : ?>
										<input type="checkbox" class="stlms-choose-existing" id="stlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $question->ID; ?>">
									<?php endif; ?>
									<label for="stlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $question->post_title ); ?><?php echo ! empty( $topic ) ? ' <strong>(' . esc_html( implode( ', ', $topic ) ) . ')</strong>' : ''; ?></label>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php esc_html_e( 'No questions found.', 'skilltriks' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<span class="spinner is-active"></span>
			<?php endif; ?>
		</div>

		<div class="stlms-qus-bank-add">
			<button class="button button-primary stlms-add-question" disabled><?php esc_html_e( 'Add', 'skilltriks' ); ?></button>
			<span class="stlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'skilltriks' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>
