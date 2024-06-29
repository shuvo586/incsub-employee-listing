<?php
/*
* Plugin Name:  Employee Listing
* Plugin URI:   https://incsub.com/employee-listing
* Description:  A example plugin to manage employee data in a custom table for Incsub Task.
* Version:      1.0.0
* Requires      at least: 5.0
* Requires PHP: 7.4
* Author:       Faisal Hossain Shuvo
* Author URI:   https://faisalshuvo.com
* License:      GPLv2 or later
* License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:  incsub-employee-listing
* Domain Path:  /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

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
	define( 'INCSUB_EMPLOYEE_LISTING_URL', plugins_url( 'cartick' ) );
}

if ( ! defined( 'CARTICK_ASSETS' ) ) {
	define( 'CARTICK_ASSETS', INCSUB_EMPLOYEE_LISTING_URL . '/assets' );
}
