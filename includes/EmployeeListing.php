<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

final class EmployeeListing {
	use Singleton;

	/**
	 * EmployeeListing Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_shortcodes' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'wp_ajax_incsub_employee_search', [ $this, 'employee_search' ] );
		add_action( 'wp_ajax_nopriv_incsub_employee_search', [ $this, 'employee_search' ] );
	}

	/**
	 * Enqueue Styles and Scripts
	 *
	 * @return void
	 */
	function enqueue_scripts() {
		wp_enqueue_style( 'incsub-employee-listing', INCSUB_EMPLOYEE_LISTING_ASSETS . '/dist/css/styles.css', '', '1.0.0' );
		wp_enqueue_script( 'incsub-employee-listing-ajax', INCSUB_EMPLOYEE_LISTING_ASSETS . '/dist/js/script.js', [ 'jquery' ], '1.0.0', true );
		wp_localize_script( 'incsub-employee-listing-ajax', 'incsub_ajax_object', [
			'ajax_url' => admin_url( 'admin-ajax.php' )
		] );
	}

	/**
	 * Register Shortcodes
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		Shortcodes::init();
	}

	/**
	 * Register Activation Hook
	 *
	 * @return void
	 */
	public static function activate() {
		self::create_tables();
	}

	/**
	 * Register Deactivation Hook
	 *
	 * @return void
	 */
	public static function deactivate() {
	}

	/**
	 * Employee table SQL Schema
	 *
	 * @return string
	 */
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
	            designation varchar(255) NOT NULL,
	            hire_date date NOT NULL,
	            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
	            PRIMARY KEY  (id)
	        ) $collate;
		";

		return $table;
	}

	/**
	 * Create Tables based on Schema
	 *
	 * @return void
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );
	}

	/**
	 * Initialize Rest API for Employee list
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		RestRoute::init();
	}

	/**
	 * Handle AJAX Employee Search
	 *
	 * @return void
	 */
	public function employee_search() {
		$query = sanitize_text_field( $_POST['query'] );
		$results = Shortcodes::search_table_data( $query );
		if ( !empty( $results ) ) : ?>
			<table class="table-auto min-w-full bg-white employee-lists__wrap">
			<thead>
			<tr>
				<th class="py-2 px-4 bg-gray-200 text-left"><?php esc_html_e( 'ID', 'incsub-employee-listing' ); ?></th>
				<th class="py-2 px-4 bg-gray-200 text-left"><?php esc_html_e( 'Name', 'incsub-employee-listing' ); ?></th>
				<th class="py-2 px-4 bg-gray-200 text-left"><?php esc_html_e( 'Email', 'incsub-employee-listing' ); ?></th>
				<th class="py-2 px-4 bg-gray-200 text-left"><?php esc_html_e( 'Designation', 'incsub-employee-listing' ); ?></th>
				<th class="py-2 px-4 bg-gray-200 text-left"><?php esc_html_e( 'Hire Date', 'incsub-employee-listing' ); ?></th>
			</tr>
			</thead>
			<tbody class="employee-lists">
			<?php foreach ( $results as $row ) : ?>
				<tr class="border-t">
					<td class="py-2 px-4 text-left"><?php echo esc_html( $row->id ); ?></td>
					<td class="py-2 px-4 text-left"><?php echo esc_html( $row->name ); ?></td>
					<td class="py-2 px-4 text-left"><?php echo esc_html( $row->email ); ?></td>
					<td class="py-2 px-4 text-left"><?php echo esc_html( $row->designation ); ?></td>
					<td class="py-2 px-4 text-left"><?php echo esc_html( $row->hire_date ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
				<p><?php esc_html_e( 'No results found.', 'incsub-employee-listing' ); ?></p>
			</div>
		<?php endif;
		wp_die();
	}
}

/**
 * Initialize main plugin
 *
 * @return bool|EmployeeListing
 */
function EmployeeListing() {
	return EmployeeListing::init();
}

EmployeeListing();