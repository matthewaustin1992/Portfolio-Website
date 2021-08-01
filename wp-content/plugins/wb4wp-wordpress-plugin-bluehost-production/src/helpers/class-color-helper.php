<?php

namespace Wb4Wp\Helpers;

/**
 * Class Color_Helper
 * @package Wb4Wp\Helpers
 */
final class Color_Helper {

	/**
	 * Turns a hexadecimal color code into an array containing RGB integers.
	 * E.g. #FFFFFF -> [255, 255, 255], #FFF -> [255, 255, 255]
	 *
	 * @param $hex string
	 *
	 * @return array|null
	 */
	public static function hex_to_rgb( $hex ) {
		$stripped = str_replace( '#', '', trim( $hex ) );

		if ( strlen( $stripped ) === 6 ) {
			$split = str_split( $stripped, 2 );

			$red   = hexdec( $split[0] );
			$green = hexdec( $split[1] );
			$blue  = hexdec( $split[2] );

			return array( $red, $green, $blue );
		} elseif ( strlen( $stripped ) === 3 ) {
			$split = str_split( $stripped, 1 );

			$red   = hexdec( $split[0] . $split[0] );
			$green = hexdec( $split[1] . $split[1] );
			$blue  = hexdec( $split[2] . $split[2] );

			return array( $red, $green, $blue );
		}

		return null;
	}

	/**
	 * Turns an array containing RGB integers into a hexadecimal color code.
	 * E.g. [255, 255, 255] -> #FFFFFF
	 *
	 * @param $rgb array
	 *
	 * @return string|null
	 */
	public static function rgb_to_hex( $rgb ) {
		if ( ! is_array( $rgb ) || count( $rgb ) !== 3 ) {
			return null;
		}

		$red   = str_pad( dechex( $rgb[0] ), 2, '0', STR_PAD_LEFT );
		$green = str_pad( dechex( $rgb[1] ), 2, '0', STR_PAD_LEFT );
		$blue  = str_pad( dechex( $rgb[2] ), 2, '0', STR_PAD_LEFT );

		return '#' . $red . $green . $blue;
	}

}
