<?php

namespace Wb4WpTheme\Managers;

use WP_Error;

/**
 * Class Update_Manager
 * @package Wb4WpTheme\Managers
 */
final class Update_Manager {

	/**
	 * Check for updates.
	 */
	public function check_for_updates() {
		add_filter( 'site_transient_update_themes', array( $this, 'do_check' ) );

		// Take over the Theme info screen on WP multisite.
		add_action( 'themes_api', array( $this, 'my_theme_api_call' ), 10, 3 );
	}

	/**
	 * @param mixed $transient Transient.
	 *
	 * @return mixed
	 */
	public function do_check( $transient ) {
		if ( empty( $transient ) ) {
			return $transient;
		}

		$theme_base    = get_option( 'stylesheet' );
		$raw_response  = wp_remote_get( WB4WP_UPDATE_URL );
		$current_theme = wp_get_theme();

		/**
		 * Only check if we're not on our local environment
		 */
		if ( $current_theme->get( 'Version' ) !== '1.0.0' && strpos( $theme_base, 'wb4wp' ) !== false ) {
			if ( ! is_wp_error( $raw_response ) && ( 200 === $raw_response['response']['code'] ) ) {
				$response = json_decode( wp_remote_retrieve_body( $raw_response ) );
			}

			// Feed the update data into WP updater.
			if ( ! empty( $response ) && version_compare( $response->new_version, $current_theme->get( 'Version' ), '!=' ) ) {
				$transient->response[ $theme_base ] = array(
					'new_version'  => $response->new_version,
					'last_updated' => $response->last_updated,
					'package'      => $response->package,
					'slug'         => $response->slug,
					'tested'       => $response->tested,
					'url'          => $current_theme->get( 'AuthorURI' ),
					'theme'        => $response->theme,
				);
			}
		}

		return $transient;
	}

	/**
	 * @param object $response The response.
	 * @param string $action The action.
	 * @param object $args The args.
	 *
	 * @return false|object|WP_Error
	 */
	public function my_theme_api_call( $response, $action, $args ) {
		$theme_base    = get_option( 'stylesheet' );
		$current_theme = wp_get_theme();

		if ( $args->slug !== $theme_base ) {
			return false;
		}

		$request = wp_remote_get( WB4WP_UPDATE_URL );

		if ( is_wp_error( $request ) ) {
			return new WP_Error( 'themes_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'wb4wp_theme' ), $request->get_error_message() );
		} else {
			$response = json_decode( wp_remote_retrieve_body( $request ) );

			return (object) array(
				'author'       => $current_theme->get( 'Author' ),
				'homepage'     => $current_theme->get( 'ThemeURI' ),
				'last_updated' => $response->last_updated,
				'name'         => $current_theme->get( 'Name' ),
				'theme_name'   => $current_theme->get( 'Name' ),
				'sections'     => array(
					'Description' => $current_theme->get( 'Description' ),
				),
				'slug'         => $current_theme->get_stylesheet(),
				'version'      => $response->new_version,
			);
		}
	}

}
