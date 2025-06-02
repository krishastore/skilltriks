<?php
/**
 * Class AssignCourseTest
 *
 * @package ST\Lms\Shortcode
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

use const ST\Lms\STLMS_COURSE_ASSIGN_BY_ME;
use const ST\Lms\STLMS_COURSE_ASSIGN_TO_ME;
use const ST\Lms\META_KEY_COURSE_ASSIGNED;

/**
 * Test the assign course form.
 */
class AssignCourseTest extends WP_Ajax_UnitTestCase {

    /**
	 * Test Assign New Course.
	 */
    public function test_assign_new_course() {
		// Create assigner and assignee users.
		$assigner_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$assignee_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		wp_set_current_user( $assigner_id );

		$course_id = $this->factory->post->create( array( 'post_type' => 'course' ) );

		$_POST['_nonce'] = wp_create_nonce( STLMS_BASEFILE );

		$completion_date = date( 'Y-m-d' );
		$_POST['assign_course_data'] = array(
			array(
				'course_id'       => $course_id,
				'user_id'         => $assignee_id,
				'completion_date' => $completion_date,
			),
		);

		// Call AJAX handler
		try {
			$this->_handleAjax( 'assign_new_course' );
		} catch ( WPAjaxDieContinueException $e ) {
			// Expected due to check_ajax_referer()
		}

		$assigned_by_me = get_user_meta( $assigner_id, STLMS_COURSE_ASSIGN_BY_ME, true );
		$this->assertArrayHasKey( "{$course_id}_{$assignee_id}", $assigned_by_me );
		$this->assertEquals( $completion_date, $assigned_by_me["{$course_id}_{$assignee_id}"] );

		$assigned_to_me = get_user_meta( $assignee_id, STLMS_COURSE_ASSIGN_TO_ME, true );
		$this->assertArrayHasKey( "{$course_id}_{$assigner_id}", $assigned_to_me );
		$this->assertEquals( $completion_date, $assigned_to_me["{$course_id}_{$assigner_id}"] );

		$assigned_users = get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true );
		$this->assertContains( $assignee_id, $assigned_users );
	}

	public function test_edit_assigned_course() {
		// Create assigner and assignee users.
		$assigner_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$assignee_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		wp_set_current_user( $assigner_id );

		// Create initial course and assign it.
		$original_course_id = $this->factory->post->create( array( 'post_type' => 'course' ) );
		$new_course_id      = $this->factory->post->create( array( 'post_type' => 'course' ) );

		$completion_date = date( 'Y-m-d' );

		// Assign course initially.
		update_user_meta( $assigner_id, STLMS_COURSE_ASSIGN_BY_ME, array(
			"{$original_course_id}_{$assignee_id}" => $completion_date,
		) );
		update_user_meta( $assignee_id, STLMS_COURSE_ASSIGN_TO_ME, array(
			"{$original_course_id}_{$assigner_id}" => $completion_date,
		) );
		update_post_meta( $original_course_id, META_KEY_COURSE_ASSIGNED, array( $assignee_id ) );

		$_POST['_nonce'] = wp_create_nonce( STLMS_BASEFILE );
		$_POST['type']   = 'edit';
		$_POST['key']    = "{$original_course_id}_{$assignee_id}";
		$_POST['id']     = $new_course_id;
		$_POST['date']   = '2025-12-31';

		try {
			$this->_handleAjax( 'update_assign_course' );
		} catch ( WPAjaxDieContinueException $e ) {
			// Expected behavior
		}

		$by_me = get_user_meta( $assigner_id, STLMS_COURSE_ASSIGN_BY_ME, true );
		$this->assertArrayHasKey( "{$new_course_id}_{$assignee_id}", $by_me );
		$this->assertEquals( '2025-12-31', $by_me["{$new_course_id}_{$assignee_id}"] );
		$this->assertArrayNotHasKey( "{$original_course_id}_{$assignee_id}", $by_me );

		$to_me = get_user_meta( $assignee_id, STLMS_COURSE_ASSIGN_TO_ME, true );
		$this->assertArrayHasKey( "{$new_course_id}_{$assigner_id}", $to_me );
		$this->assertArrayNotHasKey( "{$original_course_id}_{$assigner_id}", $to_me );

		$new_course_users = get_post_meta( $new_course_id, META_KEY_COURSE_ASSIGNED, true );
		$this->assertContains( $assignee_id, $new_course_users );

		$old_course_users = get_post_meta( $original_course_id, META_KEY_COURSE_ASSIGNED, true );
		$this->assertNotContains( $assignee_id, $old_course_users );
	}

	public function test_delete_assigned_course() {
		// Create assigner and assignee users.
		$assigner_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$assignee_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );

		wp_set_current_user( $assigner_id );

		$course_id = $this->factory->post->create( array( 'post_type' => 'course' ) );
		$completion_date = '2025-06-01';

		// Assign course manually
		update_user_meta( $assigner_id, STLMS_COURSE_ASSIGN_BY_ME, array(
			"{$course_id}_{$assignee_id}" => $completion_date,
		) );
		update_user_meta( $assignee_id, STLMS_COURSE_ASSIGN_TO_ME, array(
			"{$course_id}_{$assigner_id}" => $completion_date,
		) );
		update_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, array( $assignee_id ) );

		$_POST['_nonce'] = wp_create_nonce( STLMS_BASEFILE );
		$_POST['type']   = 'delete';
		$_POST['key']    = "{$course_id}_{$assignee_id}";
		$_POST['id']     = $course_id;

		try {
			$this->_handleAjax( 'update_assign_course' );
		} catch ( WPAjaxDieContinueException $e ) {
			// Expected behavior
		}

		$by_me = get_user_meta( $assigner_id, STLMS_COURSE_ASSIGN_BY_ME, true );
		$this->assertArrayNotHasKey( "{$course_id}_{$assignee_id}", $by_me );

		$to_me = get_user_meta( $assignee_id, STLMS_COURSE_ASSIGN_TO_ME, true );
		$this->assertArrayNotHasKey( "{$course_id}_{$assigner_id}", $to_me );

		$course_users = get_post_meta( $course_id, META_KEY_COURSE_ASSIGNED, true );
		$this->assertNotContains( $assignee_id, $course_users );
	}
}