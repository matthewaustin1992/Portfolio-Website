<?php
namespace Wb4WpTheme\Helpers;

use Wb4WpTheme\Managers\Customize\Customize_Settings;
/**
 * Font helper
 */
final class Template_Helper {
	public static function array_to_inline_css( $array ) {
		$filtered_array = array_filter(
			$array,
			function ( $value ) {
				return ! is_null( $value );
			}
		);
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		return preg_replace( array( '/{|"|}/', '/,/' ), array( ' ', ';' ), json_encode( $filtered_array ) );
	}

	public static function get_color_rules_by_index( $setting_name, $colors_map ) {
		$color_index  = ! empty( Customize_Settings::get_setting( $setting_name ) ) ? Customize_Settings::get_setting( $setting_name ) : '0';
		$rules_string = '';

		if ( ! empty( $colors_map[ $color_index ] ) ) {
			$colors       = $colors_map[ $color_index ];
			$colors       = array_map(
				function ( $value ) {
					return "var($value)";
				},
				$colors
			);
			$rules_string = self::array_to_inline_css( $colors );
		}

		return $rules_string;
	}
}
