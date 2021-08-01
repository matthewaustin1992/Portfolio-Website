<?php

namespace Wb4Wp\Managers;

use Exception;
use Raygun4php\RaygunClient;

/**
 * Class Raygun_Manager
 * @package Wb4Wp\Managers
 */
final class Raygun_Manager {

	// Lets make this a singleton.
	private static $instance;
	private $raygun_client;

	/**
	 * RaygunManager constructor.
	 */
	public function __construct() {
		$this->raygun_client = new RaygunClient( env( 'WB4WP_RAYGUN_PHP_KEY' ) );
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Raygun_Manager();
		}

		return self::$instance;
	}

	/**
	 * Transmits an error to the Raygun.io API
	 *
	 * @param int    $err_no The error number
	 * @param string $err_str The error string
	 * @param string $err_file The file the error occurred in
	 * @param int    $err_line The line the error occurred on
	 */
	public function error_handler( $err_no, $err_str, $err_file, $err_line ) {
		$this->raygun_client->SendError( $err_no, $err_str, $err_file, $err_line, self::build_tags(), self::build_custom_user_data() );
	}

	public function build_tags() {
		$tags = array();

		try {
			// Get as many meta data as possible.
			$tags['WB4WP_PLUGIN_VERSION'] = env( 'WB4WP_PLUGIN_VERSION' );
			$tags['WB4WP_ENVIRONMENT']    = env( 'WB4WP_ENVIRONMENT' );
			$tags['WB4WP_PROVIDER']       = env( 'WB4WP_PROVIDER' );
		} catch ( Exception $e ) {
			return $tags;
		}

		return $tags;
	}

	public function build_custom_user_data() {
		$user_data = array();

		try {
			// Get as many meta data as possible.
			$user_data['WB4WP_THEME_VERSION']     = env( 'WB4WP_THEME_VERSION' );
			$user_data['WB4WP_APP_URL']           = env( 'WB4WP_APP_URL' );
			$user_data['WB4WP_APP_GATEWAY_URL']   = env( 'WB4WP_APP_GATEWAY_URL' );
			$user_data['WB4WP_APP_DASHBOARD_URL'] = env( 'WB4WP_APP_DASHBOARD_URL' );

			// user data that helps us identify the error.
			$user_data['wb4wp_connected_account_id'] = get_option( 'wb4wp_connected_account_id' );
			$user_data['wb4wp_instance_uuid']        = get_option( 'wb4wp_instance_uuid' );
			$user_data['wb4wp_site_id']              = get_option( 'wb4wp_site_id' );
			$user_data['wb4wp_jwt_token']            = get_option( 'wb4wp_jwt_token' );

		} catch ( Exception $e ) {
			return $user_data;
		}

		return $user_data;
	}

	/**
	 * Transmits an exception to the Raygun.io API
	 *
	 * @param \Exception $exception An exception object to transmit
	 */
	public function exception_handler( $exception ) {
		$this->raygun_client->SendException( $exception, self::build_tags(), self::build_custom_user_data() );
	}
}
