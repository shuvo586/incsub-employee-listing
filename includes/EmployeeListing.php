<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

final class EmployeeListing {
	use Singleton;

	/**
	 * EmployeeListing Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'wp_ajax_incsub_employee_search', array( $this, 'employee_search' ) );
		add_action( 'wp_ajax_nopriv_incsub_employee_search', array( $this, 'employee_search' ) );
		add_action('wp_ajax_incsub_submit_employee', array( $this, 'employee_submit' ) );
		add_action('wp_ajax_nopriv_incsub_submit_employee', array( $this, 'employee_submit' ) );
	}

	/**
	 * Enqueue Styles and Scripts
	 *
	 * @return void
	 */
	function enqueue_scripts() {
		wp_enqueue_style( 'incsub-employee-listing', INCSUB_EMPLOYEE_LISTING_ASSETS . '/dist/css/styles.css', '', '1.0.0' );
		wp_enqueue_script( 'incsub-employee-listing-ajax', INCSUB_EMPLOYEE_LISTING_ASSETS . '/dist/js/script.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script(
			'incsub-employee-listing-ajax',
			'incsub_ajax_object',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'incsub_nonce' ),
			)
		);
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
		if ( ! isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'incsub_nonce' ) ) {
			exit( '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> No naughty business please</span></div>' );
		}

		if ( empty( $_POST['query'] ) ) {
			exit( '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> No query string provided</span></div>' );
		}

		$query   = sanitize_text_field( $_POST['query'] );
		$results = Shortcodes::search_table_data( $query );
		if ( ! empty( $results ) ) : ?>
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
		<?php else : ?>
			<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
				<p><?php esc_html_e( 'No results found.', 'incsub-employee-listing' ); ?></p>
			</div>
			<?php
		endif;
		wp_die();
	}

	/**
	 * Submit employee form
	 *
	 * @return void
	 */
	function employee_submit() {
		if ( ! isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'incsub_nonce' ) ) {
			exit( '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> No naughty business please</span></div>' );
		}

		if ( empty( $_POST['formData'] ) ) {
			exit( '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> No form data provided.</span></div>' );
		}

		parse_str( sanitize_text_field( $_POST['formData'] ), $form_fields);

		$employee_name  = sanitize_text_field( $form_fields['employee_name'] );
		$employee_email = sanitize_email( $form_fields['employee_email'] );
		$designation    = sanitize_text_field( $form_fields['designation'] );
		$hire_date      = sanitize_text_field( $form_fields['hire_date'] );

		$data = Shortcodes::insert_employee_data($employee_name, $employee_email, $designation, $hire_date);

		if ( $data ) {
			echo 'Employee data submitted successfully!';
		} else {
			echo 'Failed to submit employee data.';
		}

		wp_die();
	}
}