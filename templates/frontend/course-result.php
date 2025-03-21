<?php
/**
 * Template: Course Final Result Page
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id          = get_query_var( 'course_id', 0 );
$grade_percentage   = 0;
$curriculums        = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
$assessment         = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_ASSESSMENT, true );
$completed_results  = \ST\Lms\calculate_assessment_result( $assessment, $curriculums, $course_id );
$course_certificate = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_SIGNATURE, true );
$has_certificate    = isset( $course_certificate['certificate'] ) ? $course_certificate['certificate'] : 0;
// Get result value from array.
list( $passing_grade, $grade_percentage, $completed_on ) = $completed_results;

?>
<div class="stlms-wrap">
	<div class="stlms-lesson-view">
		<div class="stlms-lesson-view__body">
			<div class="stlms-quiz-view">
				<div class="course-result-box">
					<div class="stlms-quiz-complete">
						<img src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/certificate-<?php echo $grade_percentage >= $passing_grade ? 'pass' : 'fail'; ?>.svg" alt="">
						<?php if ( $grade_percentage >= $passing_grade ) : ?>
						<h3><?php esc_html_e( 'Congratulations on completing your course!', 'skilltriks-lms' ); ?> 🎉</h3>
						<p>
							<?php
							esc_html_e(
								'You\'ve unlocked a world of knowledge and skill. Take a moment to celebrate your
							achievement',
								'skilltriks-lms'
							);
							?>
						</p>
						<?php else : ?>
							<h3><?php esc_html_e( 'Unfortunately, This Time Wasn\'t Successful', 'skilltriks-lms' ); ?></h3>
							<p>
							<?php
							esc_html_e(
								'Every Attempt Is A Step Forward!, Don\'t Be Discouraged,
								Keep Going!',
								'skilltriks-lms'
							);
							?>
							</p>
						<?php endif; ?>
						<div class="stlms-quiz-result-list stlms-result-view">
							<div class="stlms-quiz-result-item">
								<p class="stlms-text-<?php echo $grade_percentage >= $passing_grade ? 'green' : 'red'; ?>"><?php echo esc_html( $grade_percentage ); ?>%</p>
								<span><?php esc_html_e( 'Your total Grade', 'skilltriks-lms' ); ?></span>
							</div>
						</div>
						<div class="course-certificate">
							<?php if ( $grade_percentage >= $passing_grade ) : ?>
							<span>
								<?php
									// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
									echo esc_html( sprintf( __( 'Certificate issued on %s Does not expire', 'skilltriks-lms' ), date_i18n( 'F d, Y', $completed_on ) ) );
								?>
							</span>
								<?php if ( $has_certificate ) : ?>
									<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( $course_id ); ?>"><?php esc_html_e( 'Get your Certificate', 'skilltriks-lms' ); ?></a> <i class="stlms-loader"></i>
									<?php
								endif;
							else :
								echo wp_kses(
									// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
									sprintf( __( '<a href="%s">Try Again</a>', 'skilltriks-lms' ), esc_url( get_permalink( $course_id ) ) ),
									array(
										'a' => array(
											'href' => true,
										),
									)
								);
							endif;
							?>
						</div>
						<div class="course-result-title">
							<span><?php esc_html_e( 'Course', 'skilltriks-lms' ); ?></span>
							<?php echo esc_html( get_the_title( $course_id ) ); ?>
						</div>
						<div class="stlms-quiz-result-list stlms-course-complete-result">
							<div class="stlms-quiz-result-item">
								<p><?php echo (int) \ST\Lms\calculate_assessment_result( $assessment, $curriculums, $course_id, 'lesson' ); ?>%</p>
								<span><?php esc_html_e( 'Lessons Completed', 'skilltriks-lms' ); ?></span>
							</div>
							<div class="stlms-quiz-result-item">
								<p><?php echo (int) \ST\Lms\calculate_assessment_result( $assessment, $curriculums, $course_id, 'quiz' ); ?>%</p>
								<span><?php esc_html_e( 'Quiz Completed', 'skilltriks-lms' ); ?></span>
							</div>
						</div>
						<div class="cta">
							<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>" class="stlms-btn stlms-btn-flate"><?php esc_html_e( 'Find More Courses', 'skilltriks-lms' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	if(localStorage) { // Check if the localStorage object exists
		localStorage.clear()  //clears the localstorage
	}
</script>
