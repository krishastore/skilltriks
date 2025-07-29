<?php
/**
 * Template: Notifications
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) {
	$_paged         = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$items_per_page = apply_filters( 'stlms_notification_log_per_page', get_option( 'posts_per_page' ) );
}

$notifications        = \ST\Lms\fetch_notification_data( $_paged, (int) $items_per_page );
$notification_message = \ST\Lms\notification_message();
$total_items          = $notifications['items'];
?>

<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-view">
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						<?php esc_html_e( 'Notifications', 'skilltriks' ); ?>	
					</div>
					<div class="stlms-sort-by">
						<a href="#" class="stlms-btn stlms-btn-light stlms-btn-block">
							<?php esc_html_e( 'Mark All As Read', 'skilltriks' ); ?>	
						</a>
						<button class="stlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<div class="stlms-course-view__body">
					<div class="stlms-notification-wrap">
						<ul>
							<?php
							if ( ! empty( $notifications['data'] ) ) {
								foreach ( $notifications['data'] as $notification ) :
									$from_user   = get_userdata( $notification['from_user_id'] );
									$from_name   = $from_user->display_name;
									$course_name = get_the_title( $notification['course_id'] );
									$course_link = get_permalink( $notification['course_id'] );
									$date_format = get_option( 'date_format' );
									$due_date    = ! empty( $notification['due_date'] ) ? wp_date( $date_format, strtotime( $notification['due_date'] ) ) : '';
									$time_diff   = human_time_diff( strtotime( $notification['created_at'] ), (int) current_datetime()->format( 'U' ) );
									$action_type = $notification['action_type'];
									$message     = $notification_message[ $action_type - 1 ];
									?>
								<li>
									<div class="stlms-notification-card">
										<div class="stlms-notification-image">
											<img src="<?php echo esc_url( get_avatar_url( $from_user ) ); ?>" alt="">
										</div>
										<div class="stlms-notification-content">
											<div class="stlms-notification-heading">
												<div class="stlms-notification-title">
													<?php
													echo wp_kses_post(
														wp_sprintf(
															$message,
															esc_html( $from_name ),
															esc_url( $course_link ),
															esc_html( $course_name ),
															esc_html( $due_date )
														)
													);
													?>
												</div>
												<div class="stlms-notification-time">
														<?php echo esc_html( $time_diff ) . ' ago'; ?>
												</div>
											</div>
										</div>
										<div class="stlms-notification-icon">
											<button>
												<svg width="30" height="30">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#unread-icon"></use>
												</svg>
											</button>
										</div>
									</div>
								</li>
									<?php
							endforeach;
							} else {
								?>
								<li>
									<div class="stlms-notification-card read-notification">
										No Notifications to read!
									</div>
								</li>
								<?php
							}
							?>
						</ul>
					</div>
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
									'total'     => ceil( $total_items / $items_per_page ),
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
			</div>
		</div>
	</div>
</div>