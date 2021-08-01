<?php

namespace Wb4Wp\Helpers;

use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use WP_REST_Response;

/**
 * Class Options_Helper
 * Exposes a wrapper around all the options that we register within the plugin.
 *
 * @package WB4WP\Helpers
 *
 * @access  private
 */
final class Options_Helper {

	/**
	 * Gets the generated unique id for this WP instance, or will generate a new unique id if none is present.
	 *
	 * @return string
	 */
	public static function get_instance_uuid() {
		$instance_uuid = get_option( WB4WP_INSTANCE_UUID_KEY, null );
		if ( null === $instance_uuid ) {
			$instance_uuid = uniqid();
			add_option( WB4WP_INSTANCE_UUID_KEY, $instance_uuid );
		}

		return $instance_uuid;
	}

	/**
	 * Gets the generated handshake token that should be used during setup.
	 *
	 * @return string
	 */
	public static function get_handshake_token() {
		$token      = get_option( WB4WP_INSTANCE_HANDSHAKE_TOKEN, null );
		$expiration = self::get_handshake_expiration();
		if ( null === $token || null === $expiration || $expiration < time() ) {
			$token = Guid_Helper::generate_guid();
			update_option( WB4WP_INSTANCE_HANDSHAKE_TOKEN, $token );
			update_option( WB4WP_INSTANCE_HANDSHAKE_EXPIRATION, time() + 3600 );
		}

		return $token;
	}

	/**
	 * Gets the expiration time associated with the generated handshake token.
	 *
	 * @return int|null
	 */
	public static function get_handshake_expiration() {
		return get_option( WB4WP_INSTANCE_HANDSHAKE_EXPIRATION, null );
	}

	/**
	 * Sets the plugin state
	 *
	 * @param $value -> Must be of value "new, provisioned, onboarded or suspended"
	 */
	public static function set_plugin_state( $value ) {
		$states = array(
			'new',
			'provisioned',
			'onboarded',
			'suspended',
		);

		if ( ! in_array( $value, $states, true ) ) {
			return;
		}

		update_option( WB4WP_PLUGIN_STATE, $value );
	}

	/**
	 * Unlinks all custom metadata
	 *
	 * @return void
	 */
	public static function unlink() {
		$meta_options = array(
			WB4WP_ENCRYPTION_KEY_KEY,
			WB4WP_INSTANCE_API_KEY_KEY,
			WB4WP_INSTANCE_ID_KEY,
			WB4WP_INSTANCE_UUID_KEY,
			WB4WP_CONNECTED_ACCOUNT_ID,
			WB4WP_SSO_JWT_TOKEN,
			WB4WP_SITE_ID,
			WB4WP_PLUGIN_STATE,
			WB4WP_ONBOARDING_ID,
		);

		foreach ( $meta_options as $meta_option ) {
			delete_option( $meta_option );
		}
	}

	/**
	 * Removes a single item
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function remove( $key ) {
		return delete_option( $key );
	}

	/**
	 * Collect multiple options using
	 * an array of keys
	 *
	 * A key may consist of either a method name
	 * or a meta key
	 *
	 * @param array $keys
	 *
	 * @return array|null
	 */
	public static function collect( $keys = array() ) {
		if ( count( $keys ) > 0 ) {
			$collection = array();

			foreach ( $keys as $key ) {
				$collection[ $key ] = ! empty( self::get_by_method( $key ) ) ? self::get_by_method( $key ) : self::get( $key );
			}

			return $collection;
		}

		return null;
	}

	/**
	 * Returns an option value by method
	 *
	 * @param $method
	 *
	 * @return mixed|null
	 */
	public static function get_by_method( $method ) {
		if ( ! method_exists( self::class, $method ) ) {
			return null;
		}

		return self::$method();
	}

	/**
	 * Gets a single item
	 *
	 * @param $key
	 * @param null $default
	 *
	 * @return false|mixed|void|null
	 */
	public static function get( $key, $default = null, $encrypted = false ) {
		$encrypted = false;

		if ( $encrypted ) {
			return Encryption_Helper::get_option( $key, $default );
		}
		return get_option( $key, $default );
	}

	/**
	 * Store multiple options using
	 * an array of keys and values
	 *
	 * @param array $keys
	 *
	 * @return bool
	 */
	public static function store( $keys = array() ) {
		if ( count( $keys ) > 0 ) {
			foreach ( $keys as $key => $value ) {
				if ( method_exists( self::class, $key ) ) {
					self::$key( $value );
				} else {
					self::set( $key, $value );
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Set a meta value
	 *
	 * @param $key
	 * @param $value
	 * @param false $encrypted
	 *
	 * @return bool
	 */
	public static function set( $key, $value, $encrypted = false ) {
		$encrypted = false;

		if ( $encrypted ) {
			try {
				$return_value = Encryption_Helper::update_option( $key, $value );
			} catch ( BadFormatException $e ) {
				$return_value = false;
			} catch ( EnvironmentIsBrokenException $e ) {
				$return_value = false;
			}
		} else {
			$return_value = update_option( $key, $value );
		}

		return $return_value;
	}

	public static function handle_dont_show_unpublished_changes_modal_again() {
		$success = add_option( 'wb4wp-show-unpublished-changes-modal', 'false' );

		return new WP_REST_Response(
			array(
				'success' => $success,
			),
			200
		);
	}
}
