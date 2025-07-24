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
?>

<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-view">
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						Notifications
					</div>
					<div class="stlms-sort-by">
						<a href="#" class="stlms-btn stlms-btn-light stlms-btn-block">
							Mark All As Read
						</a>
						<button class="stlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="assets/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<div class="stlms-course-view__body">
					<div class="stlms-notification-wrap">
						<ul>
							<li>
								<div class="stlms-notification-card">
									<div class="stlms-notification-image">
										<img src="./assets/images/Rectangle 26.png" alt="">
									</div>
									<div class="stlms-notification-content">
										<div class="stlms-notification-heading">
											<div class="stlms-notification-title">
												<strong>Nisarg Pandya</strong> assigned you a new course <a href="#">Code Review: Mastering the Art of Collaboration</a> with no completion date
											</div>
											<div class="stlms-notification-time">
												1 mins ago
											</div>
										</div>
									</div>
									<div class="stlms-notification-icon">
										<button>
											<svg width="30" height="30">
												<use xlink:href="assets/images/sprite-front.svg#read-icon"></use>
											</svg>
										</button>
									</div>
								</div>
							</li>
							<li>
								<div class="stlms-notification-card">
									<div class="stlms-notification-image">
										<img src="./assets/images/Rectangle 26.png" alt="">
									</div>
									<div class="stlms-notification-content">
										<div class="stlms-notification-heading">
											<div class="stlms-notification-title">
												<strong>Nisarg Pandya</strong> assigned you a new course <a href="#">Code Review: Mastering the Art of Collaboration</a> with no completion date
											</div>
											<div class="stlms-notification-time">
												1 mins ago
											</div>
										</div>
									</div>
									<div class="stlms-notification-icon">
										<button>
											<svg width="30" height="30">
												<use xlink:href="assets/images/sprite-front.svg#read-icon"></use>
											</svg>
										</button>
									</div>
								</div>
							</li>
							<li>
								<div class="stlms-notification-card read-notification">
									<div class="stlms-notification-image">
										<img src="./assets/images/Rectangle 26.png" alt="">
									</div>
									<div class="stlms-notification-content">
										<div class="stlms-notification-heading">
											<div class="stlms-notification-title">
												<strong>Nisarg Pandya</strong> assigned you a new course <a href="#">Code Review: Mastering the Art of Collaboration</a> with no completion date
											</div>
											<div class="stlms-notification-time">
												1 mins ago
											</div>
										</div>
									</div>
									<div class="stlms-notification-icon">
										<button>
											<svg width="30" height="30">
												<use xlink:href="assets/images/sprite-front.svg#unread-icon"></use>
											</svg>
										</button>
									</div>
								</div>
							</li>
							<li>
								<div class="stlms-notification-card read-notification">
									<div class="stlms-notification-image">
										<img src="./assets/images/Rectangle 26.png" alt="">
									</div>
									<div class="stlms-notification-content">
										<div class="stlms-notification-heading">
											<div class="stlms-notification-title">
												<strong>Brian Griffin</strong> assigned you a new course <a href="#"> WordPress + ChatGPT API: Taking WordPress to the outer space Infinity and Beyond with ChatGPT API, Gemini & Co-Pilot</a> with a completion date of <span>Oct. 13, 2024.</span>
											</div>
											<div class="stlms-notification-time">
												1 mins ago
											</div>
										</div>
									</div>
									<div class="stlms-notification-icon">
										<button>
											<svg width="30" height="30">
												<use xlink:href="assets/images/sprite-front.svg#unread-icon"></use>
											</svg>
										</button>
									</div>
								</div>
							</li>
							<li>
								<div class="stlms-notification-card">
									<div class="stlms-notification-image">
										<img src="./assets/images/Rectangle 26.png" alt="">
									</div>
									<div class="stlms-notification-content">
										<div class="stlms-notification-heading">
											<div class="stlms-notification-title">
												<strong>Catherine James</strong> updated the content of the course of <a href="#">HubSpot CMS for Developers – Beginners.</a>
											</div>
											<div class="stlms-notification-time">
												1 day ago
											</div>
										</div>
										<div class="stlms-notification-desc">
											<ol>
												<li>
													Course title has been modified.
												</li>
												<li>
													New lesson has been added in the course.
												</li>
												<li>
													Lesson has been removed from the course.
												</li>
												<li>
													Lesson content has been removed from the course.
												</li>
												<li>
													New quiz has been added in the course.
												</li>
												<li>
													Quiz content has been modified.
												</li>
												<li>
													Quiz has been removed from the course.
												</li>
											</ol>
										</div>
									</div>
									<div class="stlms-notification-icon">
										<button>
											<svg width="30" height="30">
												<use xlink:href="assets/images/sprite-front.svg#read-icon"></use>
											</svg>
										</button>
									</div>
								</div>
							</li>
							<li>
								<div class="stlms-notification-card read-notification">
									<div class="stlms-notification-image">
										<img src="./assets/images/Rectangle 26.png" alt="">
									</div>
									<div class="stlms-notification-content">
										<div class="stlms-notification-heading">
											<div class="stlms-notification-title">
												<strong>Brian Griffin</strong> assigned you a new course <a href="#"> WordPress + ChatGPT API: Taking WordPress to the outer space Infinity and Beyond with ChatGPT API, Gemini & Co-Pilot</a> with a completion date of <span>Oct. 13, 2024.</span>
											</div>
											<div class="stlms-notification-time">
												1 mins ago
											</div>
										</div>
									</div>
									<div class="stlms-notification-icon">
										<button>
											<svg width="30" height="30">
												<use xlink:href="assets/images/sprite-front.svg#unread-icon"></use>
											</svg>
										</button>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="stlms-course-view__footer">
					Add Pagination Here
				</div>
			</div>
		</div>
	</div>
</div>