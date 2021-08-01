<?php

namespace Wb4Wp\Helpers;

/**
 * Class Base64_Helper
 * @package Wb4Wp\Helpers
 */
class Base64_Helper {

	public static function base64_decode_recursive( $array ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return $array;
		}

		$base64_decode_recursive = function ( $value ) use ( &$base64_decode_recursive ) {
			if ( is_array( $value ) ) {
				return array_map( $base64_decode_recursive, $value );
			} elseif ( ! is_string( $value ) ) {
				return $value;
			}

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			return base64_decode( $value );
		};

		return array_map( $base64_decode_recursive, $array );
	}

	public static function base64_encode_recursive( $array ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return $array;
		}

		$base64_encode_recursive = function ( $value ) use ( &$base64_encode_recursive ) {
			if ( is_array( $value ) ) {
				return array_map( $base64_encode_recursive, $value );
			} elseif ( ! is_string( $value ) ) {
				return $value;
			}

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			return base64_encode( $value );
		};

		return array_map( $base64_encode_recursive, $array );
	}

}
