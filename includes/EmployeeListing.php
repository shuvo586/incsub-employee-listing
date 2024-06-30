<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

final class EmployeeListing {
	use Singleton;

	public function __construct() {
		add_action( 'init', [ $this, 'register_shortcodes' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'incsub_employee_listing_enqueue_scripts' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'wp_ajax_incsub_employee_search', [ $this, 'handle_ajax_employee_search' ] );
		add_action( 'wp_ajax_nopriv_incsub_employee_search', [ $this, 'handle_ajax_employee_search' ] );
	}

	function incsub_employee_listing_enqueue_scripts() {
		wp_enqueue_script( 'incsub-employee-listing-ajax', INCSUB_EMPLOYEE_LISTING_ASSETS . '/js/script.js', [ 'jquery' ], '1.0.0', true );
		wp_localize_script( 'incsub-employee-listing-ajax', 'incsub_ajax_object', [
			'ajax_url' => admin_url( 'admin-ajax.php' )
		] );
	}
	public function register_shortcodes() {
		Shortcodes::init();
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
		RestRoute::init();
	}

	public function handle_ajax_employee_search() {
		$query = sanitize_text_field( $_POST['query'] );
		$results = Shortcodes::search_table_data( $query );
		if ( !empty( $results ) ) {
			foreach ( $results as $row ) {
				echo '<tr>';
				echo '<td>' . esc_html( $row->id ) . '</td>';
				echo '<td>' . esc_html( $row->name ) . '</td>';
				echo '<td>' . esc_html( $row->email ) . '</td>';
				echo '<td>' . esc_html( $row->hire_date ) . '</td>';
				echo '<td>' . esc_html( $row->created_at ) . '</td>';
				echo '</tr>';
			}
		} else {
			echo '<p>No results found.</p>';
		}
		wp_die();
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