<?php

namespace Wb4Wp\Managers;

use Exception;
use Lcobucci\JWT\Configuration;
use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Helpers\Request_Helper;
use WP_Error;
use WP_REST_Response;

/**
 * Class Provision_Manager
 * @package Wb4Wp\Managers
 */
final class Provision_Manager {

	const CONTENT_TYPE     = 'Content-Type';
	const APPLICATION_JSON = 'application/json';
	const HEADERS          = 'headers';
	const SUCCESS          = 'success';
	const BODY             = 'body';
	const ERROR            = 'error';

	/**
	 * Starts the onboarding process
	 *
	 * @return WP_REST_Response
	 *
	 * @throws Exception
	 */
	public static function handle_start_onboarding_get() {
		try {
			$token = Options_Helper::get( WB4WP_JWT );
			if ( ! empty( $token ) ) {
				$configuration = Configuration::forUnsecuredSigner();
				$jwt           = $configuration->parser()->parse( $token );
				$claims        = $jwt->claims();
				$body          = array(
					'return_url'          => admin_url( 'admin.php?page=wb4wp-editor' ),
					'provider'            => Provider_Helper::get_provider(),
					'plugin_instantiated' => true,
					'site_id'             => $claims->get( 'siteId' ),
					'user_id'             => $claims->get( 'userId' ),
				);
			} else {
				$body = array(
					'return_url'          => admin_url( 'admin.php?page=wb4wp-editor' ),
					'provider'            => Provider_Helper::get_provider(),
					'plugin_instantiated' => true,
				);
			}

			$response = Request_Manager::post(
				Environment_Helper::get_app_gateway_url( 'wb4wp/v1.0/onboarding/model' ),
				array(
					self::HEADERS => array(
						self::CONTENT_TYPE => self::APPLICATION_JSON,
					),
					self::BODY    => $body,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$json = json_decode( $response[ self::BODY ] );

				if ( ! empty( $json->onboarding_url ) ) {
					return new WP_REST_Response(
						array(
							self::SUCCESS    => true,
							'onboarding_url' => $json->onboarding_url,
						)
					);
				}
			} else {
				Request_Helper::throw_error( wp_json_encode( $response ), __FILE__, __LINE__, 500 );
			}

			return new WP_REST_Response(
				array( self::SUCCESS => false )
			);
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	/**
	 * Authenticate SSO through our API key
	 *
	 * @return bool|WP_Error|WP_REST_Response
	 *
	 * @throws Exception
	 */
	public static function handle_sso() {
		try {
			$deps_return = null;

			$site_id = Options_Helper::get( WB4WP_SITE_ID );
			if ( empty( $site_id ) ) {
				$deps_return = Request_Helper::throw_error(
					array(
						self::SUCCESS => false,
						self::ERROR   => 'site id empty',
					),
					__FILE__,
					__LINE__,
					500
				);
			}
			$api_key = Options_Helper::get( WB4WP_INSTANCE_API_KEY_KEY, null, true );
			if ( empty( $api_key ) ) {
				$deps_return = Request_Helper::throw_error(
					array(
						self::SUCCESS => false,
						self::ERROR   => 'api key empty',
					),
					__FILE__,
					__LINE__,
					500
				);
			}
			$connected_account = Options_Helper::get( WB4WP_CONNECTED_ACCOUNT_ID );
			if ( empty( $connected_account ) ) {
				$deps_return = Request_Helper::throw_error(
					array(
						self::SUCCESS => false,
						self::ERROR   => 'connected account empty',
					),
					__FILE__,
					__LINE__,
					500
				);
			}
			$get_instance_uuid = Options_Helper::get_instance_uuid();
			if ( empty( $connected_account ) ) {
				$deps_return = Request_Helper::throw_error(
					array(
						self::SUCCESS => false,
						self::ERROR   => 'instance uuid empty',
					),
					__FILE__,
					__LINE__,
					500
				);
			}

			if ( ! empty( $deps_return ) ) {
				return $deps_return;
			}

			$post_body = array(
				'plugin_id'         => $get_instance_uuid,
				'instance_url'      => get_bloginfo( 'wpurl' ),
				'wordpress_version' => get_bloginfo( 'version' ),
				'plugin_version'    => env( 'WB4WP_PLUGIN_VERSION' ),
				'token'             => Options_Helper::get( WB4WP_JWT ), // The JWT can be optional, no empty check required.
			);

			$response = Request_Manager::post(
				Environment_Helper::get_app_gateway_url( 'wordpress/v1.0/builder/sso' ),
				array(
					self::HEADERS => array(
						self::CONTENT_TYPE => self::APPLICATION_JSON,
						'X-API-KEY'        => $api_key,
						'X-ACCOUNT-ID'     => $connected_account,
					),
					self::BODY    => $post_body,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$json = json_decode( $response[ self::BODY ] );

				if ( ! empty( $json->authentication ) ) {
					// Store the sso jwt token and ttl in the settings.
					Options_Helper::set( WB4WP_SSO_JWT_TOKEN, $json->authentication, true );
					Options_Helper::set( WB4WP_SSO_JWT_TTL, strtotime( '+60 minutes', time() ) );

					return new WP_REST_Response(
						array(
							self::SUCCESS       => true,
							'jwt'               => $json->authentication,
							'site_id'           => $site_id,
							'plugin_state'      => Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ),
							'has_onboarding_id' => ! empty( Options_Helper::get( WB4WP_ONBOARDING_ID ) ),
							'onboarding_id'     => Options_Helper::get( WB4WP_ONBOARDING_ID ),
						)
					);
				}
			}

			// phpcs:ignore
			 return Request_Helper::throw_error( wp_json_encode( $response ), __FILE__, __LINE__, 500 );
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	/**
	 * Stores a new sso JWT
	 *
	 * (this functionality is used by the WP-API
	 * which does a renewal on the JWT and sends
	 * it back to the plugin)
	 *
	 * @param $request
	 *
	 * @throws Exception
	 */
	public function update_token( $request ) {
		try {
			$json = $request->get_json_params();

			if ( ! empty( $json['token'] ) ) {
				Options_Helper::set( WB4WP_SSO_JWT_TOKEN, $json['token'], true );
			}
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	/**
	 * Deletes the onboarding_id from WP_Options
	 *
	 * @throws Exception
	 */
	public function delete_onboarding_id() {
		try {
			if ( ! Options_Helper::remove( WB4WP_ONBOARDING_ID ) ) {
				return new WP_REST_Response( 'Could not delete onboarding ID', 500 );
			}

			return new WP_REST_Response( null, 204 );
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

}
