<?php
/**
 * Declare the interface for `ST\Lms\Shortcode\Login` class.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 */

namespace ST\Lms\Interfaces;

interface Login {

	/**
	 * Main construct.
	 */
	public function __construct();

	/**
	 * Init.
	 */
	public function init();

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts );

	/**
	 * Login process.
	 */
	public function login_process();

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts();
}
