<?php
namespace Incsub\EmployeeListing;

use Incsub\EmployeeListing\Traits\Singleton;

class Shortcodes {
	use Singleton;

	public function __construct() {
		self::register();
	}

	public function register() {
		add_shortcode( 'incsub_employee_form', [ $this, 'shortcode_form' ] );
		add_shortcode( 'incsub_employee_list', [ $this, 'shortcode_list' ] );
	}

	public function shortcode_form() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && !empty( $_POST['employee_name'] ) && !empty( $_POST['employee_email'] ) && !empty( $_POST['hire_date'] ) ) {
			self::insert_data_to_table(
				sanitize_text_field( $_POST['employee_name'] ),
				sanitize_email( $_POST['employee_email'] ),
				sanitize_text_field( $_POST['hire_date'] )
			);
		}

		ob_start();
		?>
		<form method="POST">
			<input type="text" name="employee_name" required placeholder="Employee Name">
			<input type="email" name="employee_email" required placeholder="Employee Email">
			<input type="date" name="hire_date" required placeholder="Hire Date">
			<input type="submit" value="Submit">
		</form>
		<?php
		return ob_get_clean();
	}

	public function shortcode_list() {
		$data = self::get_table_data();
		ob_start();
		?>
        <form id="employee-search-form">
            <input type="text" id="employee-search" name="employee_search" placeholder="Search Employees">
            <input type="submit" value="Search">
        </form>
        <div class="overflow-x-auto border">
            <table class="table-auto min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-gray-200">ID</th>
                        <th class="py-2 px-4 bg-gray-200">Name</th>
                        <th class="py-2 px-4 bg-gray-200">Email</th>
                        <th class="py-2 px-4 bg-gray-200">Hire Date</th>
                        <th class="py-2 px-4 bg-gray-200">Created At</th>
                    </tr>
                </thead>
                <tbody class="employee-lists">
		        <?php foreach ( $data as $row ) : ?>
                    <tr class="border-t">
                        <td class="py-2 px-4"><?php echo esc_html( $row->id ); ?></td>
                        <td class="py-2 px-4"><?php echo esc_html( $row->name ); ?></td>
                        <td class="py-2 px-4"><?php echo esc_html( $row->email ); ?></td>
                        <td class="py-2 px-4"><?php echo esc_html( $row->hire_date ); ?></td>
                        <td class="py-2 px-4"><?php echo esc_html( $row->created_at ); ?></td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
		<?php
		return ob_get_clean();
	}

	public static function search_table_data( $query ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'incsub_employees';
		$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE name LIKE %s OR email LIKE %s", '%' . $wpdb->esc_like( $query ) . '%', '%' . $wpdb->esc_like( $query ) . '%' );
		return $wpdb->get_results( $sql );
	}

	public static function get_table_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'incsub_employees';
		return $wpdb->get_results( "SELECT * FROM $table_name" );
	}

	public static function insert_data_to_table( $name, $email, $hire_date ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'incsub_employees';
		$wpdb->insert( $table_name, [
			'name' => $name,
			'email' => $email,
			'hire_date' => $hire_date
		] );
	}
}
