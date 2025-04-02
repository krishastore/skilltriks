<?php
/**
 * Display capability list template.
 *
 * @package skilltriks\Lms
 */

$user_role       = isset( $_GET['role'] ) ? sanitize_text_field( wp_unslash( $_GET['role'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$user_role       = preg_replace( '/-/', '_', $user_role );
$role_caps       = ! empty( get_role( $user_role ) ) ? get_role( $user_role )->capabilities : array();
$role_name       = preg_replace( '/_/', ' ', $user_role );
$capability_list = \ST\Lms\user_capability_list();
?>

<h1><?php esc_html_e( 'Edit User Capability', 'skilltriks' ); ?></h1>
<?php
printf(
	'<h3>Add Capability to this new role:- <span>%s</span></h3>',
	esc_html( ucwords( $role_name ) )
);
?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="user-caps-form" method="post">
	<input type="hidden" name="action" value="user_caps" />
	<input type="hidden" name="role" value="<?php echo esc_html( $user_role ); ?>" />
	<?php wp_nonce_field( 'user_caps', 'user-caps-nonce' ); ?>
	<?php foreach ( $capability_list as $key => $capability ) : ?>
		<h3><?php echo esc_html( ucwords( $key ) ); ?></h3>
		<?php foreach ( $capability as $cap => $name ) : ?>
			<fieldset>
				<label for="users_can_register">
					<input name="users_can[]" type="checkbox" id="users_can_<?php echo esc_html( $cap ); ?>" value="<?php echo esc_html( $cap ); ?>" <?php echo ! empty( $role_caps ) && array_key_exists( $cap, $role_caps ) ? esc_attr( 'checked' ) : ''; ?>>
					<?php echo esc_html( $name ); ?>
				</label>
			</fieldset>
		<?php endforeach; ?>
	<?php endforeach; ?>
	<input type="submit" style="margin-top: 1em;" class="button button-primary" name="submit" value="<?php esc_html_e( 'Submit', 'skilltriks' ); ?>" />
</form>