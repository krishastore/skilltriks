<?php
/**
 * Class CollectionTest
 *
 * @package ST\Lms\Admin\Users
 */

/**
 * Collection test case.
 */
class CollectionTest extends WP_UnitTestCase {

	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	}

	/**
	 * Verify whether the CPT is registered or not.
	 */
	public function test_verify_types() {
		$this->assertTrue( post_type_exists( 'stlms_course' ) );
		$this->assertTrue( post_type_exists( 'stlms_lesson' ) );
		$this->assertTrue( post_type_exists( 'stlms_question' ) );
		$this->assertTrue( post_type_exists( 'stlms_quiz' ) );
		$this->assertTrue( post_type_exists( 'stlms_results' ) );
	}

	/**
	 * Verify whether the taxonomy is registered or not.
	 */
	public function test_verify_taxonomy() {
		$this->assertTrue( taxonomy_exists( 'stlms_course_category' ) );
		$this->assertTrue( taxonomy_exists( 'stlms_course_tag' ) );
	}
}
