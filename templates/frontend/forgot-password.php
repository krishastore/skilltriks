<?php
/**
 * Template: Forgot / Reset Password
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$error_code = isset( $_GET['message'] ) ? (int) $_GET['message'] : 0;
$email      = ! empty( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
$message    = '';

if ( 1 === $error_code ) {
	$message = __( 'Something went wrong, Please try again', 'skilltriks' );
} elseif ( 2 === $error_code ) {
	$message = __( 'Your account role is different, please contact administration.', 'skilltriks' );
} elseif ( 3 === $error_code ) {
	/* translators: %s: user email or username */
	$message = sprintf( __( 'User %s not registered in system.', 'skilltriks' ), $email );
}

// Detect if we are in reset mode.
$is_reset_mode = ( isset( $_GET['key'], $_GET['login'] ) );

// Handle reset password submission.
if ( isset( $_POST['new_password'], $_POST['login'], $_POST['key'] ) ) {
	if ( ! isset( $_POST['_stlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_stlms_nonce'] ) ), \ST\Lms\STLMS_LOGIN_NONCE ) ) {
		wp_die( esc_html__( 'Nonce verification failed.', 'skilltriks' ) );
	}

	$login        = sanitize_user( wp_unslash( $_POST['login'] ) );
	$key          = sanitize_text_field( wp_unslash( $_POST['key'] ) );
	$new_password = wp_unslash( $_POST['new_password'] ); //phpcs:ignore.

	$user = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) ) {
		wp_safe_redirect( add_query_arg( array( 'message' => 1 ) ) );
		exit;
	}

	reset_password( $user, $new_password );
	wp_safe_redirect( \ST\Lms\get_page_url( 'login' ) );
	exit;
}

// Handle forgot password submission.
if ( isset( $_POST['username'] ) && ! $is_reset_mode ) {
	if ( ! isset( $_POST['_stlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_stlms_nonce'] ) ), \ST\Lms\STLMS_LOGIN_NONCE ) ) {
		wp_die( esc_html__( 'Nonce verification failed.', 'skilltriks' ) );
	}

	$username = sanitize_text_field( wp_unslash( $_POST['username'] ) );
	$user     = get_user_by( 'email', $username );

	if ( ! $user ) {
		$user = get_user_by( 'login', $username );
	}

	if ( ! $user ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'message' => 3,
					'email'   => rawurlencode( $username ),
				)
			)
		);
		exit;
	}

	$settings   = get_option( 'stlms_settings' );
	$user_roles = ! empty( $settings['user_role'] ) ? $settings['user_role'] : array();

	if ( ! array_key_exists( reset( $user->roles ), $user_roles ) ) {
		wp_safe_redirect( add_query_arg( array( 'message' => 2 ) ) );
		exit;
	}

	$reset_email = retrieve_password( $user->user_login );
	if ( is_wp_error( $reset_email ) ) {
		wp_safe_redirect( add_query_arg( array( 'message' => 1 ) ) );
		exit;
	}

	wp_safe_redirect( \ST\Lms\get_page_url( 'login' ) );
	exit;
}
?>

<div class="stlms-wrap alignfull">
	<div class="stlms-login-wrap">
		<div class="stlms-login">
			<div class="stlms-login__header">
				<div class="stlms-login__title">
					<?php echo esc_html( $is_reset_mode ? __( 'Reset Password', 'skilltriks' ) : __( 'Forgot Password', 'skilltriks' ) ); ?>
				</div>
				<div class="stlms-login__text">
					<?php
					if ( ! $is_reset_mode ) {
						esc_html_e( 'Please enter your username or email address.', 'skilltriks' );
						echo '<br>';
						esc_html_e( 'You will receive an email message with instructions on how to reset your password.', 'skilltriks' );
					} else {
						esc_html_e( 'Please enter your new password.', 'skilltriks' );
					}
					?>
				</div>
			</div>
			<div class="stlms-forgot_password__body">
				<?php if ( ! is_admin() && ! is_user_logged_in() ) : ?>
					<form action="<?php echo esc_url( \ST\Lms\get_page_url( 'forgot_password' ) ); ?>" method="post">
						<?php wp_nonce_field( \ST\Lms\STLMS_LOGIN_NONCE, '_stlms_nonce' ); ?>
						<?php if ( $is_reset_mode ) : ?>
							<div class="stlms-form-group">
								<label class="stlms-form-label"><?php esc_html_e( 'New Password', 'skilltriks' ); ?></label>
								<input type="text" name="new_password" class="stlms-form-control" Placeholder='**********' required>
							</div>
							<input type="hidden" name="login" value="<?php echo esc_attr( sanitize_user( wp_unslash( $_GET['login'] ) ) ); ?>">
							<input type="hidden" name="key" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['key'] ) ) ); ?>">
							<div class="stlms-form-footer">
								<button type="submit" class="stlms-btn stlms-btn-block">
									<?php esc_html_e( 'Reset Password', 'skilltriks' ); ?>
									<span class="stlms-loader"></span>
								</button>
							</div>
						<?php else : ?>
							<div class="stlms-form-group">
								<label class="stlms-form-label"><?php esc_html_e( 'Username or Email Address', 'skilltriks' ); ?></label>
								<input type="text" name="username" class="stlms-form-control" placeholder="<?php esc_attr_e( 'Username or Email Address', 'skilltriks' ); ?>" required>
							</div>
							<div class="stlms-form-footer">
								<button type="submit" class="stlms-btn stlms-btn-block">
									<?php esc_html_e( 'Get New Password', 'skilltriks' ); ?>
									<span class="stlms-loader"></span>
								</button>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $message ) ) : ?>
							<div class="stlms-error-message">
								<span class="stlms-form-error"><?php echo esc_html( $message ); ?></span>
							</div>
						<?php endif; ?>
					</form>
					<?php
					else :
						wp_safe_redirect( \ST\Lms\get_page_url( 'courses' ) );
						exit;
					endif;
					?>
			</div>
		</div>
	</div>
</div>
