<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;
use WP_REST_Response;

/**
 * Class Stats_Manager
 * @package Wb4Wp\Managers
 */
final class Stats_Manager {

	const SUCCESS = 'success';

	/**
	 * Fetches the stats
	 *
	 * @param false $cron
	 *
	 * @return bool|WP_REST_Response|null
	 *
	 * @throws Exception
	 */
	public static function fetch_stats( $cron = true ) {
		try {
			$sso_jwt = Options_Helper::get( WB4WP_SSO_JWT_TOKEN, null, true );
			$return  = null;

			if ( ! empty( $sso_jwt ) ) {
				$response = Request_Manager::get(
					Environment_Helper::get_app_gateway_url( '/wb4wp/v1.0/stats' ),
					array(
						'headers' => array(
							'Cookie' => 'cp_token=' . $sso_jwt,
						),
					)
				);

				if ( ! is_wp_error( $response ) ) {
					$json = json_decode( $response['body'] );
					if ( ! empty( $json ) && ( empty( $json->success ) || true === $json->success ) ) {
						Options_Helper::set( WB4WP_STATS, $response['body'] );

						$return = true;

						if ( $cron ) {
							$return = new WP_REST_Response(
								array( self::SUCCESS => true )
							);
						}

						return $return;
					}
				}
			}

			$return = false;

			if ( $cron ) {
				$return = new WP_REST_Response(
					array( self::SUCCESS => false ),
					500
				);
			}

			return $return;
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	/**
	 * Stores dummy stats in the WP_Options
	 */
	public static function store_dummy_stats() {
		Options_Helper::set(
			WB4WP_STATS,
			// phpcs:ignore
			json_encode(
				array(
					'last_saved'              => 1604656284926,
					'initial_page_count'      => 4,
					'pages_count'             => 8,
					'initial_block_count'     => 20,
					'block_count'             => 23,
					'created_at'              => 1604656284926,
					'user_login_count'        => 5,
					'route_to_wp_admin_count' => 3,
					'site_category'           => 'Car Dealer',
					'site_topic'              => 'Cars',
					'initial_template_id'     => '125',
					'initial_template_name'   => 'business',
					'seconds_to_publish'      => '562849',
				)
			)
		);
	}

}
