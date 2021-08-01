<?php

namespace Wb4Wp\Helpers;

use Dotenv\Dotenv;
use Wb4Wp\Constants\Environment_Names;

/**
 * Class EnvironmentHelper
 *
 * @package WB4WP\Helpers
 */
final class Environment_Helper {

	const APP_URL           = 'WB4WP_APP_URL';
	const APP_GATEWAY_URL   = 'WB4WP_APP_GATEWAY_URL';
	const APP_DASHBOARD_URL = 'WB4WP_APP_DASHBOARD_URL';

	private $dotenv;

	public function __construct() {
		$this->dotenv = Dotenv::create( WB4WP_PLUGIN_DIR );
		$this->dotenv->load();
		$this->validate();
	}

	private function validate() {
		$this->dotenv->required(
			array(
				'WB4WP_PLUGIN_VERSION',
				'WB4WP_ENVIRONMENT',
				self::APP_GATEWAY_URL,
				self::APP_URL,
				self::APP_DASHBOARD_URL,
				'WB4WP_RAYGUN_PHP_KEY',
				'WB4WP_RAYGUN_JS_KEY',
			)
		);
	}

	/**
	 * Determines if the plugin is currently pointing towards a test environment.
	 *
	 * @returns bool
	 */
	public static function is_test_environment() {
		return self::get_environment() !== Environment_Names::PRODUCTION;
	}

	/**
	 * Gets the name of the environment this version of the plugin is build for.
	 *
	 * @return string
	 */
	public static function get_environment() {
		$env = env( 'WB4WP_ENVIRONMENT' );
		return '{ENV}' === $env ? Environment_Names::PRODUCTION : $env;
	}

	/**
	 * Gets the url of the app-gateway.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function get_app_gateway_url( $path = '' ) {
		$env_name = self::APP_GATEWAY_URL;
		$url      = env( $env_name );
		return $url . $path;
	}

	/**
	 * Gets the url of the app-gateway.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function get_app_url( $path = '' ) {
		$env_name = self::APP_URL;
		$url      = env( $env_name );
		return $url . $path;
	}

	/**
	 * Gets the url of the the (bluehost) dashboard
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function get_dashboard_url( $path = '' ) {
		$env_name = self::APP_DASHBOARD_URL;
		$url      = env( $env_name );
		return $url . $path;
	}
}
