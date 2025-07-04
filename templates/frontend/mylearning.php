<?php
/**
 * Template: My learning
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$search_keyword = ! empty( $_GET['_s'] ) ? sanitize_text_field( wp_unslash( $_GET['_s'] ) ) : '';
$category       = ! empty( $_GET['category'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['category'] ) ) ) : array();
$category       = array_map( 'absint', $category );
$_orderby       = ! empty( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : '';
$progress       = ! empty( $_GET['progress'] ) ? sanitize_text_field( wp_unslash( $_GET['progress'] ) ) : '';

$course_args = array(
	'post_type'      => \ST\Lms\STLMS_COURSE_CPT,
	'post_status'    => 'publish',
	'posts_per_page' => -1,
);
$_paged      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
if ( get_query_var( 'page' ) ) {
	$_paged = get_query_var( 'page' );
}
if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) {
	$course_args['paged']          = $_paged;
	$course_args['posts_per_page'] = apply_filters( 'stlms_courses_list_per_page', get_option( 'posts_per_page' ) );
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$author = ! empty( $_GET['filter_author'] ) ? (int) $_GET['filter_author'] : 0;
if ( $author ) {
	$course_args['author__in'] = array( $author );
}
if ( ! empty( $search_keyword ) ) {
	$course_args['s'] = $search_keyword;
}
if ( in_array( $_orderby, array( 'asc', 'desc' ), true ) ) {
	$course_args['orderby'] = 'title';
	$course_args['order']   = strtoupper( $_orderby );
} elseif ( 'newest' === $_orderby ) {
	$course_args['order'] = 'DESC';
} else {
	$course_args['orderby'] = 'menu_order';
}

if ( ! empty( $category ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \ST\Lms\STLMS_COURSE_CATEGORY_TAX,
		'field'    => 'term_id',
		'terms'    => $category,
		'operator' => 'IN',
	);
}

$enrol_courses         = get_user_meta( get_current_user_id(), \ST\Lms\STLMS_ENROL_COURSES, true );
$course_assigned_to_me = get_user_meta( get_current_user_id(), \ST\Lms\STLMS_COURSE_ASSIGN_TO_ME, true ) ? get_user_meta( get_current_user_id(), \ST\Lms\STLMS_COURSE_ASSIGN_TO_ME, true ) : array();
$due_soon              = get_option( 'stlms_settings' );
$due_soon              = ! empty( $due_soon['due_soon'] ) ? $due_soon['due_soon'] : '';

?>

<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-filter">
				<button class="stlms-filter-toggle stlms-filter-close">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<form method="get" onsubmit="return false;" class="stlms-filter-form">
					<div class="stlms-accordion">
						<div class="stlms-accordion-item" data-expanded="true">
							<div class="stlms-accordion-header">
								<div class="stlms-accordion-filter-title">
									<?php esc_html_e( 'Filters', 'skilltriks' ); ?>
								</div>
							</div>
							<?php
							$terms_list = \ST\Lms\course_taxonomies( \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
							?>
							<div class="stlms-accordion-collapse">
								<div class="stlms-pt-20">
									<div class="stlms-form-group">
										<label class="stlms-form-label"><?php esc_html_e( 'By Categories', 'skilltriks' ); ?></label>
										<select class="stlms-form-control category">
											<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
											<?php foreach ( $terms_list as $key => $term_level ) : ?>
												<option value="<?php echo esc_attr( $term_level['id'] ); ?>" <?php selected( reset( $category ), $term_level['id'] ); ?>><?php echo esc_html( $term_level['name'] ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<?php
									$course_status = \ST\Lms\course_statistics();
									$total_course  = 0;
									$max_num_page  = 0;
									$has_course    = false;

									if ( ! empty( $enrol_courses ) ) {
										$course_args['post__in'] = $enrol_courses;
										$has_course              = true;
									}
									if ( ! empty( $progress ) ) {
										if ( ! empty( $course_status[ $progress ] ) ) {
											$course_args['post__in'] = $course_status[ $progress ];
										} else {
											$has_course = false;
										}
									}
									if ( $has_course ) {
										$courses      = new \WP_Query( $course_args );
										$total_course = $courses->found_posts;
										$max_num_page = $courses->max_num_pages;
									}

									$statistics = array(
										'total_courses'    => $course_status['total_course'],
										'course_completed' => count( $course_status['completed'] ),
										'course_in_progress' => count( $course_status['in_progress'] ),
										'course_not_started' => count( $course_status['not_started'] ),
									);
									?>
									<div class="stlms-form-group">
										<label class="stlms-form-label"><?php esc_html_e( 'By Progress', 'skilltriks' ); ?></label>
										<select class="stlms-form-control progress">
											<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
											<?php
											foreach ( $statistics as $key => $value ) :
												if ( 'total_courses' !== $key ) :
													$_key = str_replace( 'course_', '', $key );
													?>
													<option value="<?php echo esc_attr( $_key ); ?>" <?php selected( $progress, str_replace( 'course ', '', $_key ) ); ?>><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></option>
													<?php
												endif;
											endforeach;
											?>
										</select>
									</div>
									<button class="stlms-reset-btn"><?php esc_html_e( 'Reset', 'skilltriks' ); ?></button>
								</div>
							</div>
							<input type="hidden" name="category" value="<?php echo esc_attr( (string) reset( $category ) ); ?>">
							<input type="hidden" name="_s" value="<?php echo esc_attr( $search_keyword ); ?>">
							<input type="hidden" name="progress" value="<?php echo esc_attr( $progress ); ?>">
							<input type="hidden" name="order_by" value="<?php echo esc_attr( $_orderby ); ?>">
						</div>
					</div>
				</form>
			</div>
			<div class="stlms-course-view" id="stlms_course_view">
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						<?php
						echo wp_kses(
							sprintf(
								// Translators: %d total courses.
								__( 'My Learnings <span>(%s Courses)</span>', 'skilltriks' ),
								esc_html( (string) $statistics['total_courses'] )
							),
							array(
								'span' => array(),
							)
						);
						?>
					</div>
					<div class="stlms-sort-by">
						<form onsubmit="return false;">
							<select>
								<option value=""><?php esc_html_e( 'Sort By', 'skilltriks' ); ?></option>
								<option value="asc"<?php selected( $_orderby, 'asc' ); ?>><?php esc_html_e( 'Alphabetically (A To Z)', 'skilltriks' ); ?></option>
								<option value="desc"<?php selected( $_orderby, 'desc' ); ?>><?php esc_html_e( 'Alphabetically (Z To A)', 'skilltriks' ); ?></option>
								<option value="newest"<?php selected( $_orderby, 'newest' ); ?>><?php esc_html_e( 'Newest', 'skilltriks' ); ?></option>
							</select>
						</form>
						<button class="stlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<div class="stlms-course-view__body">
					<div class="stlms-statistics">
						<ul>
							<?php
							foreach ( $statistics as $key => $value ) :
								$stat_title = ucwords( str_replace( '_', ' ', $key ) );
								?>
								<li>
									<div class="stlms-statistics__title"><?php echo esc_html( $stat_title ); ?></div>
									<div class="stlms-statistics__no"><?php echo esc_html( $value ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php if ( $has_course && $courses->have_posts() ) : ?>
						<div class="stlms-course-list">
							<ul>
								<?php
								while ( $courses->have_posts() ) :
									$courses->the_post();

									$course_id        = get_the_ID();
									$get_terms        = get_the_terms( $course_id, \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
									$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
									$curriculums      = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
									$total_lessons    = 0;
									$total_quizzes    = 0;
									$course_view_link = get_the_permalink();
									$course_link      = $course_view_link;
									$button_text      = esc_html__( 'Start Learning', 'skilltriks' );
									$extra_class      = '';
									$course_progress  = '0%';
									$has_certificate  = 0;
									if ( ! empty( $curriculums ) ) {
										$lessons          = \ST\Lms\get_curriculums( $curriculums, \ST\Lms\STLMS_LESSON_CPT );
										$total_lessons    = count( $lessons );
										$quizzes          = \ST\Lms\get_curriculums( $curriculums, \ST\Lms\STLMS_QUIZ_CPT );
										$total_quizzes    = count( $quizzes );
										$total_duration   = \ST\Lms\count_duration( array_merge( $lessons, $quizzes ) );
										$curriculums      = \ST\Lms\merge_curriculum_items( $curriculums );
										$curriculums      = array_keys( $curriculums );
										$first_curriculum = reset( $curriculums );
										$first_curriculum = explode( '_', $first_curriculum );
										$first_curriculum = array_map( 'absint', $first_curriculum );
										$section_id       = reset( $first_curriculum );
										$item_id          = end( $first_curriculum );
										if ( is_user_logged_in() ) {
											$meta_key       = sprintf( \ST\Lms\STLMS_COURSE_STATUS, get_the_ID() );
											$user_id        = get_current_user_id();
											$current_status = get_user_meta( $user_id, $meta_key, true );
											if ( ! empty( $current_status ) ) {
												$course_progress = \ST\Lms\calculate_course_progress( get_the_ID(), $curriculums, $current_status ) . '%';
												$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
												$current_status  = explode( '_', $current_status );
												$section_id      = (int) reset( $current_status );
												$item_id         = (int) end( $current_status );
												$button_text     = esc_html__( 'Continue Learning', 'skilltriks' );
												$extra_class     = ' stlms-btn-light';
												$last_curriculum = end( $curriculums );
												$last_curriculum = explode( '_', $last_curriculum );
												$last_curriculum = array_map( 'absint', $last_curriculum );
												if ( reset( $last_curriculum ) === $section_id && end( $last_curriculum ) === $item_id ) {
													$restart_course = \ST\Lms\restart_course( get_the_ID() );
													if ( $restart_course ) {
														$first_curriculum   = reset( $curriculums );
														$first_curriculum   = explode( '_', $first_curriculum );
														$first_curriculum   = array_map( 'absint', $first_curriculum );
														$section_id         = reset( $first_curriculum );
														$item_id            = end( $first_curriculum );
														$button_text        = esc_html__( 'Restart Course', 'skilltriks' );
														$extra_class        = ' stlms-btn-dark';
														$course_certificate = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_SIGNATURE, true );
														$has_certificate    = isset( $course_certificate['certificate'] ) ? $course_certificate['certificate'] : 0;
													}
												}
											}
										}
										$curriculum_type = get_post_type( $item_id );
										$curriculum_type = str_replace( 'stlms_', '', $curriculum_type );
										$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_view_link ), $section_id, $curriculum_type, $item_id );

										$button_text = apply_filters( 'stlms_course_view_button_text', $button_text );
										$course_link = apply_filters( 'stlms_course_view_button_link', $course_link );

										$existing_users = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_ASSIGNED, true ) ? get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_ASSIGNED, true ) : array();
										?>
										<li>
											<div class="stlms-course-item">
												<div class="stlms-course-item__img">
													<?php if ( ! empty( $terms_name ) ) : ?>
														<div class="stlms-course-item__tag">
															<span><?php echo esc_html( $terms_name ); ?></span>
														</div>
													<?php endif; ?>
													<?php
													if ( in_array( get_current_user_id(), $existing_users, true ) ) :

														foreach ( $course_assigned_to_me as $key => $date ) {
															if ( strpos( $key, "{$course_id}_" ) === 0 ) {
																$completion_date = $date;
																break;
															}
														}
														$completion_date = strtotime( $completion_date );
														$date_format     = get_option( 'date_format' );
														$due_soon_day    = '-' . $due_soon . ' day';
														$due_date        = strtotime( $due_soon_day, $completion_date );
														$formatted_date  = wp_date( $date_format, $completion_date );
														?>
													<div class="stlms-course-item__tag due-tag">
														<?php
														if ( ! empty( $completion_date ) && '100%' !== $course_progress ) :
																$today_timestamp     = (int) current_datetime()->format( 'U' );
																$formatted_timestamp = strtotime( $formatted_date );
															?>
															<?php if ( $today_timestamp >= $due_date && $today_timestamp <= $formatted_timestamp ) : ?>	
																<span class="due-soon-tag">
																	<?php esc_html_e( 'Due Soon', 'skilltriks' ); ?>
																</span>
															<?php endif; ?>
															<?php if ( $today_timestamp > $formatted_timestamp ) : ?>	
																<span class="due-tag">
																	<?php esc_html_e( 'Due', 'skilltriks' ); ?>
																</span>
																<?php
															endif;
														endif;
														?>
													</div>
													<?php endif; ?>
													<a href="<?php echo esc_url( $course_view_link ); ?>">
														<?php if ( has_post_thumbnail() ) : ?>
															<?php the_post_thumbnail(); ?>
														<?php else : ?>
															<img fetchpriority="high" decoding="async" src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
														<?php endif; ?>
													</a>
												</div>
												<div class="stlms-course-item__info">
													<div class="stlms-course-item__by">
														<?php
															echo wp_kses(
																sprintf(
																	// Translators: %1$s to filter url, %2$s author name.
																	__( 'by <a href="%1$s">%2$s</a>', 'skilltriks' ),
																	add_query_arg(
																		array(
																			'filter_author' => get_the_author_meta( 'ID' ),
																		)
																	),
																	get_the_author_meta( 'display_name' )
																),
																array(
																	'a' => array(
																		'href' => true,
																		'class' => true,
																		'target' => true,
																	),
																)
															);
														?>
													</div>
													<h3 class="stlms-course-item__title"><a href="<?php echo esc_url( $course_link ); ?>"><?php the_title(); ?></a></h3>
													<div class="stlms-course-item__meta">
														<ul>
															<li>
																<svg width="16" height="16">
																	<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#clock">
																	</use>
																</svg>
																<?php
																$duration_str = \ST\Lms\seconds_to_decimal_hours( $total_duration );
																if ( ! empty( $duration_str ) ) {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	echo esc_html( sprintf( __( '%s Hours', 'skilltriks' ), $duration_str ) );
																} else {
																	echo esc_html__( 'Lifetime', 'skilltriks' );
																}
																?>
															</li>
															<li>
																<svg width="16" height="16">
																	<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
																	</use>
																</svg>
																<?php
																if ( $total_lessons > 1 ) {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	echo esc_html( sprintf( __( '%s Lessons', 'skilltriks' ), $total_lessons ) );
																} else {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	echo esc_html( sprintf( __( '%s Lesson', 'skilltriks' ), $total_lessons ) );
																}
																?>
															</li>
															<li>
																<svg width="16" height="16">
																	<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#quiz">
																	</use>
																</svg>
																<?php
																if ( $total_quizzes > 1 ) {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	echo esc_html( sprintf( __( '%s Quizzes', 'skilltriks' ), $total_quizzes ) );
																} else {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	echo esc_html( sprintf( __( '%s Quiz', 'skilltriks' ), $total_quizzes ) );
																}
																?>
															</li>
														</ul>
													</div>
													<div class="stlms-progress">
														<div class="stlms-progress__label">
															<?php
                                                            // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
															echo esc_html( sprintf( __( '%s Complete', 'skilltriks' ), $course_progress ) );
															?>
														</div>
														<div class="stlms-progress__bar">
															<div class="stlms-progress__bar-inner" style="width: <?php echo esc_attr( $course_progress ); ?>"></div>
														</div>
													</div>
													<div class="stlms-course-item__action">
														<a href="<?php echo esc_url( $course_link ); ?>" class="stlms-btn stlms-btn-block<?php echo esc_attr( $extra_class ); ?>" ><?php echo esc_html( $button_text ); ?></a>
														<?php if ( $has_certificate && '100%' === $course_progress ) : ?>
															<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( (string) $course_id ); ?>" class="stlms-btn stlms-btn-block download-certificate"><?php esc_html_e( 'Download certificate', 'skilltriks' ); ?></a>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</li>
										<?php
									}
								endwhile;
								?>
							</ul>
						</div>
					<?php elseif ( ! empty( $search_keyword ) ) : ?>
						<div class="stlms-text-xl stlms-p-16 stlms-bg-gray stlms-text-center stlms-text-primary-dark"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'skilltriks' ); ?> <a href="<?php echo esc_url( \ST\Lms\get_page_url( 'my_learning' ) ); ?>"><?php esc_html_e( 'Back to my learning', 'skilltriks' ); ?>.</a></div>
					<?php else : ?>
						<div class="stlms-text-xl stlms-p-16 stlms-bg-gray stlms-text-center stlms-text-primary-dark"><?php esc_html_e( 'No courses were found.', 'skilltriks' ); ?></div>
					<?php endif; ?>
				</div>
				<?php if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) : ?>
					<div class="stlms-course-view__footer">
						<div class="stlms-pagination">
							<?php
							$big            = 999999999;
							$paginate_links = paginate_links(
								array(
									'base'      => str_replace( (string) $big, '%#%', get_pagenum_link( $big ) ),
									'format'    => '?paged=%#%',
									'current'   => max( 1, $_paged ),
									'total'     => $max_num_page,
									'prev_text' => '',
									'next_text' => '',
								)
							);
							if ( $paginate_links ) {
								echo wp_kses_post( $paginate_links );
							}
							?>
						</div>
					</div>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</div>
