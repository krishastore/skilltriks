<?php
/**
 * Template: Assign Course To Me.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stlms_users = get_users(
	array(
		'fields'       => array( 'display_name' ),
		'role__not_in' => array( 'Administrator' ),
	)
);

$course_assigned_to_me = get_user_meta( get_current_user_id(), 'course_assigned_to_me', true ) ? get_user_meta( get_current_user_id(), 'course_assigned_to_me', true ) : array();
?>

<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-filter">
				<button class="stlms-filter-toggle">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<div class="stlms-accordion">
					<div class="stlms-accordion-item" data-expanded="true">
						<div class="stlms-accordion-header">
							<div class="stlms-accordion-filter-title">
								<?php esc_html_e( 'Filters', 'skilltriks' ); ?>
							</div>
						</div>
						<div class="stlms-accordion-collapse">
							<div class="stlms-pt-20">
								<div class="stlms-form-group">
									<label class="stlms-form-label"><?php esc_html_e( 'By Progress', 'skilltriks' ); ?></label>
									<select class="stlms-form-control">
										<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
										<option value="Not Started"><?php esc_html_e( 'Not Started', 'skilltriks' ); ?></option>
										<option value="In Progress"><?php esc_html_e( 'In Progress', 'skilltriks' ); ?></option>
										<option value="Completed"><?php esc_html_e( 'Completed', 'skilltriks' ); ?></option>									
									</select>
								</div>
								<div class="stlms-form-group">
									<label class="stlms-form-label"><?php esc_html_e( 'By Assigner', 'skilltriks' ); ?></label>
									<select class="stlms-form-control">
										<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
										<?php foreach ( $stlms_users as $users ) : ?>
										<option value="<?php echo esc_html( $users->display_name ); ?>"><?php echo esc_html( $users->display_name ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<button class="stlms-reset-btn"><?php esc_html_e( 'Reset', 'skilltriks' ); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="stlms-course-view">
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						<?php esc_html_e( 'Assigned Course', 'skilltriks' ); ?>
					</div>
					<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
					<div class="stlms-sort-by">
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_new_course' ) ); ?>" class="stlms-btn ">
							<?php esc_html_e( 'Assign New Course', 'skilltriks' ); ?>
						</a>
						<button class="stlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
					<?php endif; ?>
				</div>
				<div class="stlms-course-view__body">
					<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
					<div class="stlms-assigned-course__header">
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_by_me' ) ); ?>" class="stlms-assigned-course__btn">
							<span>
								<?php esc_html_e( 'Assigned By Me', 'skilltriks' ); ?>
							</span>
						</a>
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_to_me' ) ); ?>" class="stlms-assigned-course__btn active">
							<span>
								<?php esc_html_e( 'Assigned To Me', 'skilltriks' ); ?>
							</span>
						</a>
					</div>
					<?php endif; ?>
					<div class="stlms-datatable">
						<table id="myTable" class="stripe row-border" style="width:100%">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Course Assigned', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Assigned By', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Completion Date', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Progress Status', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Actions', 'skilltriks' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $course_assigned_to_me as $key => $completion_date ) :

									list( $course_id, $_user_id ) = explode( '_', $key, 2 );

									$user_info       = get_userdata( $_user_id );
									$completion_date = strtotime( $completion_date );
									$formatted_date  = wp_date( 'M. j, Y', $completion_date );
									$current_status  = get_user_meta( $_user_id, sprintf( \ST\Lms\STLMS_COURSE_STATUS, $course_id ), true );
									$curriculums     = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
									$curriculums     = \ST\Lms\merge_curriculum_items( \ST\Lms\get_curriculums() );
									$curriculums     = array_keys( $curriculums );
									$course_progress = \ST\Lms\calculate_course_progress( $_user_id, $curriculums, $current_status ) . '%';

									if ( '100%' === $course_progress ) {
										$course_status = 'Completed';
									} elseif ( '0%' === $course_progress ) {
										$course_status = 'Not Started';
									} else {
										$course_status = 'In Progress';
									}
									?>
								<tr>
									<td>
										<a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>" class="stlms-datatable__course-link">
											<?php echo esc_html( get_the_title( $course_id ) ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $user_info->display_name ); ?></td>
									<td>
										<div class="due-date">
											<?php echo esc_html( $formatted_date ); ?>
											<?php if ( ! empty( $completion_date ) ) : ?>
											<span class="due-soon-tag">
												<?php esc_html_e( 'Due Soon', 'skilltriks' ); ?>
											</span>
											<?php endif; ?>
										</div>
									</td>
									<td>
										<div class="stlms-progress">
											<?php echo esc_html( $course_status . '(' . $course_progress . ')' ); ?>
											<div class="stlms-progress__bar">
												<div class="stlms-progress__bar-inner" style="width: <?php echo esc_html( $course_progress ); ?>"></div>
											</div>
										</div>
									</td>
									<td><a href="#" class="stlms-btn stlms-btn-light">Start</a></td>
								</tr>
									<?php
								endforeach;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>