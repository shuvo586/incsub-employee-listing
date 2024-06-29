<?php
/**
 * Singleton Class trait
 *
 * @package incsub-employee-listing
 * @author Faisal Hossain Shuvo <contact@faisalshuvo.com>
 */

namespace Incsub\EmployeeListing\Traits;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Singleton {
	/**
	 * Instance Variable
	 *
	 * @var $instance
	 */
	protected static $instance;

	/**
	 * Init instance
	 *
	 * @return bool|static
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Singleton Trait Constructor
	 */
	protected function __construct() {}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'incsub-employee-listing' ), '1.0.0' );
	}

	/**
	 * Un-serializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Un-serializing instances of this class is forbidden.', 'incsub-employee-listing' ), '1.0.0' );
	}
}

// End of file Singleton.php.
