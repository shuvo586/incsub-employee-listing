<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

final class EmployeeListing {
	use Singleton;

	public function __construct() {
		//add_action( 'init', [ $this, 'register_shortcodes' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	public function register_shortcodes() {
		Shortcodes::get_instance()->register();
	}

	public static function activate() {
		self::create_tables();
	}

	public static function deactivate() {
	}

	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		/** @noinspection PhpUnnecessaryLocalVariableInspection */
		/** @noinspection SqlNoDataSourceInspection */

		$table = "
			CREATE TABLE {$wpdb->prefix}incsub_employees (
	            id mediumint(9) NOT NULL AUTO_INCREMENT,
	            name varchar(255) NOT NULL,
	            email varchar(255) NOT NULL,
	            hire_date date NOT NULL,
	            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
	            PRIMARY KEY  (id)
	        ) $collate;
		";

		return $table;
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );
	}

	public function register_rest_routes() {
		RestApi::get_instance()->register_routes();
	}
}

/**
 * Initialize main plugin
 *
 * @return false|EmployeeListing
 */
function EmployeeListing() {
	return EmployeeListing::init();
}

EmployeeListing();