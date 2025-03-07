<?php
/**
 * The file that manage the user capabilities.
 *
 * @link       https://getskilltriks.com
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin\Users
 */

namespace ST\Lms\Admin\Users;

/**
 * Capability manage class.
 */
class Capability extends \WP_List_Table {

	/**
	 * Pagination per page.
	 *
	 * @var int $per_page.
	 */
	public $per_page;

	/**
	 * Public constructor.
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct();

		$this->per_page = 20;
		$this->setting_enqueue_scripts();
	}

	/**
	 * Entry Data
	 *
	 * @param string $search_by_name Role search by name.
	 * @return array|null
	 */
	public function get_roles( $search_by_name ) {
		$per_page = $this->per_page;
		$settings = get_option( 'stlms_settings' );
		$data     = isset( $settings['user_role'] ) && ! empty( $settings['user_role'] ) ? $settings['user_role'] : array();

		if ( ! empty( $search_by_name ) ) {
			$result = array_filter(
				$data,
				function ( $value ) use ( $search_by_name ) {
					return $value == $search_by_name; //phpcs:ignore.Universal.Operators.StrictComparisons.LooseEqual
				},
				ARRAY_FILTER_USE_BOTH
			);
			return $result;
		}
		return $data;
	}

	/**
	 * Enqueue setting scripts and styles.
	 */
	public function setting_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_style( \ST\Lms\STLMS_SETTING );
		wp_enqueue_script( \ST\Lms\STLMS_SETTING );
	}
}
