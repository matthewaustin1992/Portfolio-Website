<?php

namespace Wb4Wp\Helpers;

use Wb4Wp\Constants\Provider_Names;

/**
 * Class Provider_Helper
 * @package Wb4Wp\Helpers
 */
final class Provider_Helper {

	private static $providers = array(
		Provider_Names::BLUEHOST       => 'Bluehost Website Builder',
		Provider_Names::BLUEHOST_INDIA => 'Bluehost India Website Builder',
		Provider_Names::BLUEHOST_ASIA  => 'Bluehost Asia Website Builder',
		Provider_Names::WB4WP          => 'WebsiteBuilder',
	);

	/**
	 * Retrieves the plugin provider's name
	 *
	 * @return string
	 */
	public static function get_provider_name() {
		return self::$providers[ self::get_provider() ];
	}

	/**
	 * Retrieves the plugin provider
	 *
	 * @return false|mixed|string|void
	 */
	public static function get_provider() {
		$fallback_provider = Provider_Names::WB4WP;

		$env_file_provider = env( 'WB4WP_PROVIDER' );

		if ( $env_file_provider ) {
			$fallback_provider = $env_file_provider;
		}

		return Options_Helper::get( WB4WP_PROVIDER, $fallback_provider );
	}

	public static function is_bluehost() {
		$current_provider = self::get_provider();

		return Provider_Names::BLUEHOST === $current_provider ||
			Provider_Names::BLUEHOST_INDIA === $current_provider ||
			Provider_Names::BLUEHOST_ASIA === $current_provider;
	}

	public static function is_wb_4_wp() {
		return Provider_Names::WB4WP === self::get_provider();
	}

	/**
	 * Returns a list of providers
	 *
	 * @return string[]
	 */
	public static function get_providers() {
		return array_values( self::$providers );
	}
}
