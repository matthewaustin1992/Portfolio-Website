<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Helpers\Options_Helper;
use WP_Error;

/**
 * Class Instance_Manager
 * @package Wb4Wp\Managers
 */
final class Instance_Manager {

	/**
	 * Handles the callback from the WordPress API and will store all the instance details.
	 *
	 * @param $request
	 *
	 * @return bool|WP_Error
	 *
	 * @throws Exception
	 */
	public function handle_callback( $request ) {
		try {
			$account_information = json_decode( $request->get_body() );
			if ( null === $account_information ) {
				return new WP_Error( 'rest_bad_request', 'Invalid account details', array( 'status' => 400 ) );
			}

			Options_Helper::set( WB4WP_INSTANCE_API_KEY_KEY, $account_information->api_key, true );
			Options_Helper::store(
				array(
					WB4WP_CONNECTED_ACCOUNT_ID => $account_information->account_id,
					WB4WP_SITE_ID              => $account_information->site_id,
				)
			);
			Options_Helper::set_plugin_state( 'provisioned' );

			return true;
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}
}
