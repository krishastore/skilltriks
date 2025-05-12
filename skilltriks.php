<?php
/**
 * Plugin Name:     SkillTriks
 * Plugin URI:      https://wordpress.org/plugins/skilltriks/
 * Description:     A Comprehensive Solution For Training Management. Contact Us For More Details On Training Management System.
 * Author:          KrishaWeb
 * Author URI:      https://www.skilltriks.com/
 * Text Domain:     skilltriks
 * License:         GPLv3 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Version:         1.0
 *
 * @package         ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vendor_file = __DIR__ . '/vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	require_once $vendor_file;
}

define( 'STLMS_BASEFILE', __FILE__ );
define( 'STLMS_VERSION', '1.0.0' );
define( 'STLMS_ABSURL', plugins_url( '/', STLMS_BASEFILE ) );
define( 'STLMS_BASENAME', plugin_basename( STLMS_BASEFILE ) );
define( 'STLMS_ABSPATH', dirname( STLMS_BASEFILE ) );
define( 'STLMS_DIRNAME', basename( STLMS_ABSPATH ) );
define( 'STLMS_TEMPLATEPATH', STLMS_ABSPATH . '/templates' );
define( 'STLMS_ASSETS', STLMS_ABSURL . 'assets' );

/**
 * Plugin textdomain.
 */
function stlms_textdomain() {
	load_plugin_textdomain( 'skilltriks', false, basename( __DIR__ ) . '/languages' );
}
add_action( 'plugins_loaded', 'stlms_textdomain', 20 );

/**
 * Plugin activation.
 */
function stlms_activation() {
	\ST\Lms\Helpers\Utility::activation_hook();
}
register_activation_hook( STLMS_BASEFILE, 'stlms_activation' );

/**
 * Plugin deactivation.
 */
function stlms_deactivation() {
	\ST\Lms\Helpers\Utility::deactivation_hook();
}
register_deactivation_hook( STLMS_BASEFILE, 'stlms_deactivation' );

/**
 * Initialization class.
 */
function stlms_init() {
	$stlms = stlms_run();
	if ( is_callable( array( $stlms, 'init' ) ) ) {
		$stlms->init();
	}
}
add_action( 'plugins_loaded', 'stlms_init' );

/**
 * Init.
 */
function stlms_run() {
	if ( ! class_exists( '\ST\Lms\Core' ) ) {
		return null;
	}
	return ST\Lms\Core::instance();
}
