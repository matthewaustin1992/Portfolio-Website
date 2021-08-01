<?php

namespace Wb4Wp\Managers;

/**
 * Class Update_Manager
 * @package Wb4Wp\Managers
 */
final class Update_Manager {

	public function add_hooks() {
		/**
		 * Only check if we're not on our local environment
		 */
		if ( strpos( $this->get_version(), '{BUILD}' ) !== false ) {
			return;
		}

		/**
		 * JSON from WB4WP Version JSON
		 */
		$json = wp_remote_get( env( 'WB4WP_PLUGIN_UPDATE_JSON' ) );

		add_filter(
			'site_transient_update_plugins',
			function ( $transient ) use ( $json ) {
				if ( is_wp_error( $json ) || ! is_object( $transient ) ) {
					return $transient;
				}

				/**
				 * Decoded JSON from WB4WP Version JSON
				 */
				$release = json_decode( wp_remote_retrieve_body( $json ) );

				if ( ! property_exists( $release, 'new_version' )
					|| ( ! property_exists( $transient, 'response' ) || ! property_exists( $transient, 'no_update' ) )
				) {
					return $transient;
				}

				if ( version_compare( $release->new_version, $this->get_version(), '!=' ) ) {
					// TODO: Remove once we remove the backwards compatibility in the version.json.
					$release->url = 'https://www.newfold.com';
					$transient->response[ plugin_basename( WB4WP_PLUGIN_FILE ) ] = $release;
				} else {
					$transient->no_update[ plugin_basename( WB4WP_PLUGIN_FILE ) ] = (object) array(
						'id'            => plugin_basename( WB4WP_PLUGIN_FILE ),
						'slug'          => basename( plugin_dir_path( WB4WP_PLUGIN_FILE ) ),
						'plugin'        => plugin_basename( WB4WP_PLUGIN_FILE ),
						'new_version'   => $this->get_version(),
						'url'           => 'https://www.newfold.com',
						'package'       => '',
						'icons'         => array(),
						'banners'       => array(),
						'banners_rtl'   => array(),
						'tested'        => '',
						'requires_php'  => '5.4',
						'compatibility' => new \stdClass(),
					);
				}

				return $transient;
			}
		);

		add_action(
			'plugins_api',
			function ( $response, $action, $args ) use ( $json ) {
				if ( is_wp_error( $json ) ) {
					return $response;
				}

				if ( isset( $args->slug ) && 'wb4wp-wordpress-plugin' === $args->slug ) {
					/**
					 * Decoded JSON from WB4WP Version JSON
					 */
					$release = json_decode( wp_remote_retrieve_body( $json ) );

					$response = (object) array(
						'author'       => $this->get_plugin_header( 'author' ),
						'homepage'     => $this->get_plugin_header( 'uri' ),
						'last_updated' => $release->last_updated,
						'name'         => $this->get_plugin_header( 'name' ),
						'plugin_name'  => $this->get_plugin_header( 'name' ),
						'sections'     => array(
							'Description' => $this->get_plugin_header( 'description' ),
						),
						'slug'         => $this->get_plugin_header( 'slug' ),
						'version'      => $release->new_version,
					);
				}

				return $response;
			},
			20,
			3
		);
	}

	private function get_version() {
		return strstr( $this->get_plugin_header( 'version' ), ' ', true );
	}

	private function get_plugin_header( $name ) {
		/**
		 * A collection of valid WordPress plugin file headers.
		 *
		 * @var array
		 */
		$headers = array(
			'author'               => 'Author',
			'author_uri'           => 'AuthorURI',
			'description'          => 'Description',
			'domain_path'          => 'DomainPath',
			'license'              => 'License',
			'license_uri'          => 'LicenseURI',
			'name'                 => 'Name',
			'requires_wp_version'  => 'RequiresAtLeast',
			'requires_php_version' => 'RequiresPHP',
			'text_domain'          => 'TextDomain',
			'uri'                  => 'PluginURI',
			'version'              => 'Version',
		);

		$value = '';
		if ( array_key_exists( $name, $headers ) ) {
			$value = $this->get_file_header( $headers[ $name ] );
		}

		return $value;
	}

	/**
	 * Get a specific plugin file header.
	 *
	 * @param string $name The plugin file header name.
	 *
	 * @return string
	 */
	private function get_file_header( $name ) {
		$file_headers = $this->get_file_headers();

		return (string) isset( $file_headers[ $name ] ) ? $file_headers[ $name ] : '';
	}

	/**
	 * Get all plugin file headers.
	 *
	 * @return array
	 */
	private function get_file_headers() {
		static $file_headers = array();

		if ( empty( $file_headers ) ) {

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require wp_normalize_path( ABSPATH . '/wp-admin/includes/plugin.php' );
			}

			$file_headers = get_plugin_data( WB4WP_PLUGIN_FILE );
		}

		return $file_headers;
	}
}
