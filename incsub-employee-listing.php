<?php
/*
* Plugin Name:  Employee Listing
* Plugin URI:   https://incsub.com/employee-listing
* Description:  A example plugin to manage employee data in a custom table for Incsub Task.
* Version:      1.0.0
* Requires      at least: 5.0
* Requires PHP: 7.2
* Author:       Faisal Hossain Shuvo
* Author URI:   https://faisalshuvo.com
* License:      GPLv2 or later
* License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:  incsub-employee-listing
* Domain Path:  /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'INCSUB_EMPLOYEE_LISTING_VERSION' ) ) {
	define( 'INCSUB_EMPLOYEE_LISTING_VERSION', '1.0.0' );
}

if ( ! defined( 'INCSUB_EMPLOYEE_LISTING_FILE' ) ) {
	define( 'INCSUB_EMPLOYEE_LISTING_FILE', __FILE__ );
}

if ( ! defined( 'INCSUB_EMPLOYEE_LISTING_DIR' ) ) {
	define( 'INCSUB_EMPLOYEE_LISTING_DIR', plugin_dir_path( INCSUB_EMPLOYEE_LISTING_FILE ) );
}

if ( ! defined( 'INCSUB_EMPLOYEE_LISTING_URL' ) ) {
	define( 'INCSUB_EMPLOYEE_LISTING_URL', plugins_url( 'incsub-employee-listing' ) );
}

if ( ! defined( 'INCSUB_EMPLOYEE_LISTING_ASSETS' ) ) {
	define( 'INCSUB_EMPLOYEE_LISTING_ASSETS', INCSUB_EMPLOYEE_LISTING_URL . '/assets' );
}

/**
 * Composer autoload
 */

if ( file_exists( INCSUB_EMPLOYEE_LISTING_DIR . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Plugin Initializer.
	 */
	function incsub_employee_listing() {
		Incsub\EmployeeListing\EmployeeListing::init();
	}

	add_action( 'plugins_loaded', 'incsub_employee_listing' );

	register_activation_hook( INCSUB_EMPLOYEE_LISTING_FILE, array( 'Incsub\EmployeeListing\EmployeeListing', 'activate' ) );
	register_deactivation_hook( INCSUB_EMPLOYEE_LISTING_FILE, array( 'Incsub\EmployeeListing\EmployeeListing', 'deactivate' ) );
} else {
	add_action(
		'admin_notices',
		function () {
			?>
			<div class="notice notice-error notice-alt">
				<p><?php esc_html_e( 'Cannot initialize “Employee Listing” plugin. <code>vendor/autoload.php</code> is missing. Please run <code>composer dump-autoload -o</code> within the this plugin directory.', 'incsub-employee-listing' ); ?></p>
			</div>
			<?php
		}
	);
}
