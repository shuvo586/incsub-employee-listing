<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

class RestRoute {
	use Singleton;

	/**
	 * class RestRoute constructor
	 */
	public function __construct() {
		self::register_routes();
	}

	/**
	 * Register new rest route
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'incsub-employee-listing/v1',
			'/employees',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_employees' ),
			)
		);

		register_rest_route(
			'incsub-employee-listing/v1',
			'/employees',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'insert_employee' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Get employee list
	 *
	 * @return \WP_REST_Response
	 */
	public function get_employees() {
		$data = Shortcodes::get_table_data();
		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Insert New Employee
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function insert_employee( \WP_REST_Request $request ) {
		$name        = sanitize_text_field( $request->get_param( 'name' ) );
		$email       = sanitize_email( $request->get_param( 'email' ) );
		$designation = sanitize_text_field( $request->get_param( 'designation' ) );
		$hire_date   = sanitize_text_field( $request->get_param( 'hire_date' ) );
		if ( ! empty( $name ) && ! empty( $email ) && ! empty( $designation ) && ! empty( $hire_date ) ) {
			Shortcodes::insert_data_to_table( $name, $email, $designation, $hire_date );
			return new \WP_REST_Response( 'Employee data inserted', 201 );
		}
		return new \WP_REST_Response( 'Invalid data', 400 );
	}
}
