<?php

namespace Wb4Wp\Helpers;

/**
 * Class Debug_Helper
 * @package Wb4Wp\Helpers
 */
final class Debug_Helper {

	/**
	 * Logs the exception
	 *
	 * @param $exception
	 */
	public static function log( $exception ) {
		if (
			method_exists( $exception, 'getFile' ) &&
			method_exists( $exception, 'getLine' ) &&
			method_exists( $exception, 'getMessage' )
		) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $exception->getFile() . ':' . $exception->getLine() . ' - ' . $exception->getMessage() );
		}
	}
}
