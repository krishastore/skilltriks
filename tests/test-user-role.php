<?php
/**
 * Class UserRoleTest
 *
 * @package ST\Lms\Admin\MetaBoxes
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

/**
 * User role test case.
 */
class UserRoleTest extends WP_UnitTestCase {

	/**
	 * instance of setting options.
	 */
	public $options;

	/**
	 * Role name.
	 */
	public $role_name = 'Project Manager';

	/**
	 * Role key name.
	 */
	public $role_key  = 'project_manager';


	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		do_action( 'init' );
		$user_id = $this->factory->user->create(
			array(
				'role' => 'subscriber',
			)
		);
		$this->assertIsInt( $user_id );
		wp_set_current_user( $user_id );

		$this->options = new \ST\Lms\Helpers\SettingOptions();
	}

	/**
	 * Create test role.
	 */
	public function test_create_role() {
		$_POST['user-role-nonce'] = wp_create_nonce( 'user_role' );
		$_POST['user_role']       = $this->role_name;

		// Capture wp_redirect.
		add_filter( 'wp_redirect', '__return_false' );
		$this->options->stlms_new_user_role();

		$user_role = $this->options->get_option( 'user_role' );

		$this->assertSame( $this->role_name, $user_role[ $this->role_key ] );
	}

	/**
	 * test role capabilities.
	 */
	public function test_role_capabilities() {
		$default_caps 			 = ! empty( get_role( 'author' ) ) ? get_role( 'author' )->capabilities : array( 'read' => true );
		$user_caps               = array(	
			'edit_courses',
			'edit_lessons',
			'edit_questions',
			'edit_quizzes',
		);
		$caps       			  = array_merge( $user_caps, $default_caps );
		$_POST['user-caps-nonce'] = wp_create_nonce( 'user_caps' );
		$_POST['role'] 			  =	$this->role_key; 
		$_POST['users_can']		  = $caps;

		
		$this->options->options['user_role'][ $this->role_key ] = $this->role_name;

		// Capture wp_redirect.
		add_filter( 'wp_redirect', '__return_false' );
		$this->options->stlms_user_capabilities();

		$role = get_role( $this->role_key );
		$this->assertNotNull( $role );
		$this->assertSame( $this->role_key, $role->name );

		foreach ( $user_caps as $cap ) {
			$this->assertArrayHasKey( $cap, $role->capabilities, "Capability $cap not found in role" );
			$this->assertTrue( $role->has_cap( $cap ), "Capability $cap not active" );
		}
	}

	/**
	 * Assign custom role to user.
	 */
	public function test_assign_custom_role_to_user() {
		$current_user = wp_get_current_user();
		$current_user->set_role( $this->role_key );

		$this->assertContains( $this->role_key, $current_user->roles );
		$this->assertTrue( current_user_can( 'edit_courses' ) );
		$this->assertTrue( current_user_can( 'edit_lessons' ) );
		$this->assertTrue( current_user_can( 'edit_questions' ) );
		$this->assertTrue( current_user_can( 'edit_quizzes' ) );
	}

}
