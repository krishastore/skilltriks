<?php
/**
 * The file that defines the user management functionality.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin\Users
 */

namespace ST\Lms\Admin\Users;

use const ST\Lms\STLMS_USER_DEPARTMENTS;
use const ST\Lms\STLMS_COURSE_TAXONOMY_DEP;

/**
 * Users manage class.
 */
class Users extends \ST\Lms\Admin\Core implements \ST\Lms\Interfaces\AdminCore {

	/**
	 * Store capability list class object.
	 *
	 * @var object|null $capability_list
	 * @since 1.0.0
	 */
	public $capability_list = null;

	/**
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
		add_action( 'user_new_form', array( $this, 'stlms_user_departments_dropdown' ), 10, 1 );
		add_action( 'show_user_profile', array( $this, 'stlms_user_departments_dropdown' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'stlms_user_departments_dropdown' ), 10, 1 );
		add_action( 'user_register', array( $this, 'stlms_save_user_departments' ), 10, 1 );
		add_action( 'personal_options_update', array( $this, 'stlms_save_user_departments' ), 10, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'stlms_save_user_departments' ), 10, 1 );
	}

	/**
	 * Registration of admin submenu.
	 */
	public function register_admin_menu() {
		$hook = add_submenu_page(
			'skilltriks',
			__( 'User Role Editor', 'skilltriks' ),
			__( 'User Role Editor', 'skilltriks' ),
			apply_filters( 'skilltriks/menu/capability', 'manage_options' ),
			'stlms_manage_roles',
			array( $this, 'render_menu_page' )
		);
		add_action( "load-$hook", array( $this, 'load_menu_page' ) );
	}

	/**
	 * Load submenu page..
	 */
	public function load_menu_page() {
		// Include WP_List_Table class file.
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		// Call the required model.
		$this->capability_list = new \ST\Lms\Admin\Users\CapabilityList();
	}

	/**
	 * Render admin menu page.
	 */
	public function render_menu_page() {
		require_once STLMS_TEMPLATEPATH . '/admin/users/capability-list.php';
	}

	/**
	 * Display department dropdown in user profile and add new user page.
	 *
	 * @param string|object $operation Current operation.
	 */
	public function stlms_user_departments_dropdown( $operation ) {
		if ( is_string( $operation ) && 'add-new-user' !== $operation ) {
			return;
		}

		$user_department = is_object( $operation )
			? get_user_meta( $operation->ID, STLMS_USER_DEPARTMENTS, true )
			: '';

		$dep_list = get_terms(
			array(
				'taxonomy'   => STLMS_COURSE_TAXONOMY_DEP,
				'hide_empty' => false,
				'fields'     => 'id=>name',
			)
		);
		?>
		
		<h3><?php esc_html_e( 'Additional Information', 'skilltriks' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="<?php echo esc_attr( STLMS_USER_DEPARTMENTS ); ?>"><?php esc_html_e( 'Department', 'skilltriks' ); ?></label></th>
				<td>
					<select name="<?php echo esc_attr( STLMS_USER_DEPARTMENTS ); ?>" id="<?php echo esc_attr( STLMS_USER_DEPARTMENTS ); ?>">
						<option value=""><?php esc_html_e( 'Select Department', 'skilltriks' ); ?></option>
						<?php if ( ! is_wp_error( $dep_list ) && ! empty( $dep_list ) ) : ?>
							<?php foreach ( $dep_list as $dep_id => $dep_name ) : ?>
								<option value="<?php echo absint( $dep_id ); ?>" <?php selected( $user_department, $dep_id ); ?>>
									<?php echo esc_html( $dep_name ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Please select user department.', 'skilltriks' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save user department on profile update and new user creation.
	 *
	 * @param int $user_id User ID.
	 */
	public function stlms_save_user_departments( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( isset( $_POST[ STLMS_USER_DEPARTMENTS ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			update_user_meta( $user_id, STLMS_USER_DEPARTMENTS, absint( $_POST[ STLMS_USER_DEPARTMENTS ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
	}
}
