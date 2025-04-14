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

<div id="assign_quiz" class="hidden" style="max-width:463px">
	<div class="stlms-qus-bank-modal">
		<div class="stlms-tab-container">
			<div class="stlms-tabs-nav">
				<button class="stlms-tab active" data-tab="assign-quiz-list" data-filter_type="all"><?php esc_html_e( 'All', 'skilltriks' ); ?></button>
				<button class="stlms-tab" data-tab="assign-quiz-list" data-filter_type="most_used"><?php esc_html_e( 'Most Used', 'skilltriks' ); ?></button>
			</div>

			<div class="stlms-tab-content active" data-tab="assign-quiz-list">
				<input type="text"
					placeholder="<?php esc_attr_e( 'Type here to search for the quiz', 'skilltriks' ); ?>"
					class="stlms-qus-bank-search">
				<div class="stlms-qus-list" id="stlms_quiz_list">
				<?php
				if ( ! empty( $fetch_request ) ) :
					$args = array(
						'posts_per_page' => -1,
						'post_type'      => \ST\Lms\STLMS_QUIZ_CPT,
						'post_status'    => 'publish',
					);
					if ( isset( $type ) && 'most_used' === $type ) {
						$popular_ids = wp_popular_terms_checklist( \ST\Lms\STLMS_QUIZ_TAXONOMY_LEVEL_1, 0, 10, false );
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						$args['tax_query'][] = array(
							'taxonomy' => \ST\Lms\STLMS_QUIZ_TAXONOMY_LEVEL_1,
							'field'    => 'term_id',
							'terms'    => $popular_ids,
							'operator' => 'IN',
						);
					}
					$quizzes  = get_posts( $args );
					$quiz_ids = get_posts(
						array(
							'post_type'    => \ST\Lms\STLMS_QUIZ_CPT,
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
							'meta_key'     => \ST\Lms\META_KEY_QUIZ_QUESTION_IDS,
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
							'meta_value'   => array( $question_id ),
							'meta_compare' => 'REGEXP',
							'fields'       => 'ids',
						)
					);
					$quiz_ids = ! empty( $quiz_ids ) ? $quiz_ids : array();
					?>
					<?php if ( ! empty( $quizzes ) ) : ?>
					<ul class="stlms-qus-list-scroll">
						<?php
						foreach ( $quizzes as $key => $quiz ) :
							?>
						<li>
							<div class="stlms-setting-checkbox">
								<input type="checkbox" class="stlms-choose-quiz" id="stlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $quiz->ID; ?>" <?php echo esc_attr( in_array( $quiz->ID, $quiz_ids, true ) ? 'checked' : '' ); ?>>
								<label for="stlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $quiz->post_title ); ?></label>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
						<p><?php esc_html_e( 'No quiz found.', 'skilltriks' ); ?></p>
					<?php endif; ?>
				<?php else : ?>
					<span class="spinner is-active"></span>
			<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="stlms-qus-bank-add">
			<button class="button button-primary stlms-add-quiz"
				disabled><?php esc_html_e( 'Save', 'skilltriks' ); ?></button>
			<span
				class="stlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'skilltriks' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>
