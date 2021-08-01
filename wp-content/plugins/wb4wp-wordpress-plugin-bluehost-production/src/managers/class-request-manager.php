<?php

namespace Wb4Wp\Managers;

use WP_Error;

/**
 * Class Request_Manager
 * @package Wb4Wp\Managers
 */
final class Request_Manager {

	const HEADERS            = 'headers';
	const BODY               = 'body';
	private static $timeout  = 100;
	private static $blocking = true;
	private static $headers  = array(
		'Content-Type' => 'application/json',
	);

	/**
	 * Do a post request
	 *
	 * @param string $url
	 * @param array $args
	 *
	 * @return array|WP_Error
	 */
	public static function post( $url, array $args ) {
		if ( ! empty( $args[ self::HEADERS ] ) ) {
			self::$headers = array_merge( self::$headers, $args[ self::HEADERS ] );
			unset( $args[ self::HEADERS ] );
		}

		$default = array(
			'method'      => 'POST',
			'timeout'     => self::$timeout,
			self::HEADERS => self::$headers,
			self::BODY    => wp_json_encode( array() ),
			'blocking'    => self::$blocking,
		);

		if ( ! empty( $args[ self::BODY ] ) ) {
			$default[ self::BODY ] = wp_json_encode( $args[ self::BODY ] );
			unset( $args[ self::BODY ] );
		}

		return wp_remote_post( $url, array_merge( $default, $args ) );
	}

	/**
	 * Do a get request
	 *
	 * @param string $url
	 * @param array $args
	 *
	 * @return array|WP_Error
	 */
	public static function get( $url, array $args ) {
		$default = array(
			'method'      => 'GET',
			'timeout'     => self::$timeout,
			self::HEADERS => array(),
			'blocking'    => self::$blocking,
		);

		return wp_remote_get( $url, array_merge( $default, $args ) );
	}

	public static function get_logged_in_user( $request ) {
		if ( empty( $request ) ) {
			return null;
		}

		preg_match( '/wordpress_logged_in_[^=]+=([^|]+)/', urldecode( $request->get_header( 'cookie' ) ), $matches );
		$logged_in_user_username = end( $matches );

		return get_user_by( 'login', $logged_in_user_username );
	}

	public static function get_logged_in_user_id( $request, $default = 0 ) {
		$user = self::get_logged_in_user( $request );
		if ( empty( $user ) ) {
			return $default;
		}

		return $user->ID;
	}

}
