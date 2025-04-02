<?php
/**
 * Template: Userinfo shortcode.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) :
	$userinfo   = wp_get_current_user();
	$logout_url = wp_logout_url( \ST\Lms\get_page_url( 'login' ) );
	?>
	<div class="stlms-user">
		<div class="stlms-user-photo">
			<div class="stlms-photo">
				<?php echo get_avatar( $userinfo->ID ); ?>
			</div>
		</div>
		<div class="stlms-user-info">
			<span class="stlms-user-name"><?php echo esc_html( $userinfo->display_name ); ?></span>
			<div class="stlms-user-dd">
				<div class="stlms-user-dd__toggle">
					<?php esc_html_e( 'My Account', 'skilltriks' ); ?>
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-down"></use>
					</svg>
				</div>
				<div class="stlms-user-dd__menu">
					<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'my_learning' ) ); ?>" class="stlms-user-dd__link"><?php esc_html_e( 'My Learnings', 'skilltriks' ); ?></a>
					<a href="<?php echo esc_url( $logout_url ); ?>" class="stlms-user-dd__link"><?php esc_html_e( 'Logout', 'skilltriks' ); ?></a>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>
	<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'login' ) ); ?>" class="stlms-btn stlms-btn-block"><?php esc_html_e( 'Login', 'skilltriks' ); ?></a>
<?php endif; ?>