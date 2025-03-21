<?php
/**
 * Display capability list template.
 *
 * @package skilltriks\Lms
 */

$options     = get_option( 'stlms_settings' );
$user_role   = isset( $_GET['role'] ) ? sanitize_text_field( wp_unslash( $_GET['role'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$user_role   = preg_replace( '/-/', '_', $user_role );
$role_exists = ! empty( $options['user_role'] ) ? array_key_exists( $user_role, $options['user_role'] ) : false;

if ( ! $role_exists ) {
	?>
	<div class="wrap">
		<h2 style="display:inline-block; margin-right: 5px;"><?php esc_html_e( 'User capability roles', 'skilltriks-lms' ); ?></h2>
		<a href="javascript:;" class="page-title-action stlms-add-new-role"><?php esc_html_e( 'Add New Role', 'skilltriks-lms' ); ?></a>
		<hr class="wp-header-end">
		<form method="get">
			<?php $this->capability_list->prepare_items(); ?>
			<p class="search-box">
				<input type="hidden" name="page" value="stlms_manage_roles">
				<label class="screen-reader-text" for="search_email-search-input"><?php esc_html_e( 'Search:', 'skilltriks-lms' ); ?></label>
				<input type="search" id="search_email-search-input" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>" placeholder="<?php esc_attr_e( 'Search by role name', 'skilltriks-lms' ); ?>">
				<input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'skilltriks-lms' ); ?>">
			</p>
			<?php $this->capability_list->display(); ?>
		</form>
	</div>

	<div id="add-new-role-modal" class="hidden" style="max-width:400px">
		<div class="stlms-import-data">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="stlms-role-form" method="post">
				<input type="hidden" name="action" value="user_role" />
				<?php wp_nonce_field( 'user_role', 'user-role-nonce' ); ?>
				<div class="stlms-role-input">
					<label for="user-role"><?php esc_html_e( 'Add New Role:', 'skilltriks-lms' ); ?></label>
					<input type="text" id="user-role" name="user_role" class="regular-text" required>
				</div>
				<div class="stlms-role-action">
					<input type="submit" class="button button-primary" name="submit" value="<?php esc_html_e( 'Submit', 'skilltriks-lms' ); ?>" />
				</div>
			</form>
		</div>
	</div>
	<?php
} else {
	require_once STLMS_TEMPLATEPATH . '/admin/users/capability-edit.php';
}
