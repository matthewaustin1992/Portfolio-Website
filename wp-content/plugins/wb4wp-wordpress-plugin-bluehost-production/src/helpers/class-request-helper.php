<?php

namespace Wb4Wp\Helpers;

use Raygun4php\RaygunClient;
use WP_Error;
use WP_REST_Response;

/**
 * Class RequestHelper
 *
 * @package Wb4Wp\Helpers
 */
final class Request_Helper {

	/**
	 * Returns a WP_Error or WR_REST_Response
	 * as response and logs the error to Raygun
	 *
	 * @param $err_msg
	 * @param $err_file
	 * @param $err_line
	 * @param $status_code
	 * @param bool        $wp
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public static function throw_error( $err_msg, $err_file, $err_line, $status_code, $wp = false ) {
		global $raygun_client;
		if ( ! $raygun_client ) {
			$raygun_client = new RaygunClient( env( 'WB4WP_RAYGUN_PHP_KEY' ) );
		}

		if ( $raygun_client ) {
			$raygun_client->SendError(
				$status_code,
				is_array( $err_msg ) ? wp_json_encode( $err_msg ) : $err_msg,
				$err_file,
				$err_line
			);
		}

		if ( $wp ) {
			return new WP_Error( 'rest_error', $err_msg, array( 'status' => $status_code ) );
		}

		return new WP_REST_Response( $err_msg, $status_code );
	}

}
