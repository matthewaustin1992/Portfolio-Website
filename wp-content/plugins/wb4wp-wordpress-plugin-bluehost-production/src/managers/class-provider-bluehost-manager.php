<?php

namespace Wb4Wp\Managers;

use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Request_Helper;
use WP_Error;
use WP_REST_Response;

/**
 * Class Provider_Bluehost_Manager
 * @package Wb4Wp\Managers
 */
final class Provider_Bluehost_Manager {

	const CONTENT_TYPE     = 'Content-Type';
	const APPLICATION_JSON = 'application/json';
	const HEADERS          = 'headers';
	const SUCCESS          = 'success';

	/**
	 * Temporarily authenticate using an email address
	 *
	 * @param string $token
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public static function provision( $token ) {
		if ( ! empty( $token ) ) {
			$body = array(
				'plugin_id'         => Options_Helper::get_instance_uuid(),
				'instance_url'      => get_bloginfo( 'wpurl' ),
				'wordpress_version' => get_bloginfo( 'version' ),
				'plugin_version'    => env( 'WB4WP_PLUGIN_VERSION' ),
				'token'             => $token,
				'lander_type'       => Options_Helper::get( WB4WP_LANDER_TYPE, 'wb4wp-page-builder-sso-flow' ),
			);

			$response = Request_Manager::post(
				Environment_Helper::get_app_gateway_url( 'wordpress/v1.0/builder' ),
				array(
					self::HEADERS => array(
						self::CONTENT_TYPE => self::APPLICATION_JSON,
					),
					'body'        => $body,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$json = json_decode( $response['body'] );

				if ( ! empty( $json->api_key ) ) {
					// Store the account information in the settings.
					// TODO use the instance manager.
					Options_Helper::set( WB4WP_INSTANCE_API_KEY_KEY, $json->api_key, true );
					Options_Helper::store(
						array(
							WB4WP_CONNECTED_ACCOUNT_ID => $json->account_id,
							WB4WP_SITE_ID              => $json->site_id,
						)
					);

					if ( Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ) === 'new' ) {
						Options_Helper::set_plugin_state( 'provisioned' );
					}

					return new WP_REST_Response(
						array(
							self::SUCCESS => true,
						)
					);
				}
			} else {
				// Invalid JWT Token.
				return Request_Helper::throw_error(
					array(
						self::SUCCESS => false,
						'code'        => 1001,
					),
					__FILE__,
					__LINE__,
					500
				);
			}
		}

		// Invalid JWT Token.
		return Request_Helper::throw_error(
			array(
				self::SUCCESS => false,
				'code'        => 1000,
			),
			__FILE__,
			__LINE__,
			500
		);
	}
}
