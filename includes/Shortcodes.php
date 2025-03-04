<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

class Shortcodes {
	use Singleton;

	/**
	 * Class Shortcodes constructor
	 */
	public function __construct() {
		self::register();
	}

	/**
	 * Register new shortcodes
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'incsub_employee_form', array( $this, 'shortcode_form' ) );
		add_shortcode( 'incsub_employee_list', array( $this, 'shortcode_list' ) );
	}

	/**
	 * Employee form shortcode callback
	 *
	 * @return false|string
	 */
	public function shortcode_form() {
		ob_start();
		?>
		<div class="bg-white p-4 rounded-lg shadow-lg">
			<form id="employee-submit-form" method="POST" class="space-y-4">
				<div class="form-message"></div>
				<div>
					<label for="employee_name" class="sr-only"><?php esc_html_e( 'Employee Name', 'incsub-employee-listing' ); ?></label>
					<input type="text" id="employee_name" name="employee_name" required placeholder="Employee Name" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>
				<div>
					<label for="employee_email" class="sr-only"><?php esc_html_e( 'Employee Email', 'incsub-employee-listing' ); ?></label>
					<input type="email" id="employee_email" name="employee_email" required placeholder="Employee Email" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>
				<div>
					<label for="designation" class="sr-only"><?php esc_html_e( 'Designation', 'incsub-employee-listing' ); ?></label>
					<input type="text" id="designation" name="designation" required placeholder="Designation" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>
				<div>
					<label for="hire_date" class="sr-only"><?php esc_html_e( 'Hire Date', 'incsub-employee-listing' ); ?></label>
					<input type="date" id="hire_date" name="hire_date" required placeholder="Hire Date" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>
				<input type="hidden" name="incsub_nonce" value="<?php echo esc_attr( wp_create_nonce('incsub_nonce') ); ?>">
				<input type="submit" value="Submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Employee list shortcode callback
	 *
	 * @return false|string
	 */
	public function shortcode_list() {
		$data = self::get_table_data();
		ob_start();
		?>
		<div class="container mx-auto">
			<div class="bg-white p-4 rounded-lg shadow-lg">
				<?php if ( empty( $data ) ) : ?>
					<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
						<p class="font-bold"><?php esc_html_e( 'Notice', 'incsub-employee-listing' ); ?></p>
						<p><?php esc_html_e( 'There are no employees to display. Please use the form below to submit new employee information.', 'incsub-employee-listing' ); ?></p>
					</div>
				<?php else : ?>
					<div class="mb-4">
						<form id="employee-search-form" class="flex space-x-2">
							<label for="employee-search" class="sr-only"><?php esc_html_e( 'Search Employees', 'incsub-employee-listing' ); ?></label>
							<input type="text" id="employee-search" name="employee_search" placeholder="Search Employees" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
							<input type="hidden" name="incsub_nonce" value="<?php echo esc_attr( wp_create_nonce( 'incsub_nonce' ) ); ?> ">
							<input type="submit" value="Search" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
						</form>
					</div>
					<div class="overflow-x-auto border">
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
							<?php foreach ( $data as $row ) : ?>
								<tr class="border-t">
									<td class="py-2 px-4 text-left"><?php echo esc_html( $row->id ); ?></td>
									<td class="py-2 px-4 text-left"><?php echo esc_html( $row->name ); ?></td>
									<td class="py-2 px-4 text-left"><?php echo esc_html( $row->email ); ?></td>
									<td class="py-2 px-4 text-left"><?php echo esc_html( $row->designation ); ?></td>
									<td class="py-2 px-4 text-left"><?php echo esc_html( $row->hire_date ); ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Employee search form results
	 *
	 * @param $query
	 *
	 * @return array|object|null
	 */
	public static function search_table_data( $query ) {
		// phpcs:disable
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}incsub_employees WHERE name LIKE %s OR email LIKE %s", '%' . $wpdb->esc_like( $query ) . '%', '%' . $wpdb->esc_like( $query ) . '%' );
		return $wpdb->get_results( $sql );
        // phpcs:enable
	}

	/**
	 * Get table data
	 *
	 * @return array|object|null
	 */
	public static function get_table_data() {
		// phpcs:disable
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}incsub_employees" );
		// phpcs:enable
	}

	/**
	 * Insert data into table
	 *
	 * @param $name
	 * @param $email
	 * @param $designation
	 * @param $hire_date
	 *
	 * @return bool|int|\mysqli_result|null
	 */
	public static function insert_employee_data( $name, $email, $designation, $hire_date ) {

        // phpcs:disable
		global $wpdb;
		return $wpdb->insert(
			$wpdb->prefix . 'incsub_employees',
			array(
				'name'        => $name,
				'email'       => $email,
				'designation' => $designation,
				'hire_date'   => $hire_date,
			)
		);
        // phpcs:enable
	}
}
