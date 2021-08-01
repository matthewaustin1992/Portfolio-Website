<?php

namespace Wb4WpTheme\Managers;

use Wb4WpTheme\Managers\Customize\Customize_Settings;

/**
 * Manages the templates
 * Class Color_Manager
 *
 * @package Wb4WpTheme\Managers
 */
final class Color_Manager {

	const COLOR_BLACK = '#000000';
	const COLOR_WHITE = '#ffffff';

	const MIN_CONTRAST_RATIO = 4.5; // https://www.w3.org/TR/WCAG20/#visual-audio-contrast-contrast .

	/**
	 * Get background colo by name.
	 *
	 * @param string $color_name color name.
	 *
	 * @return bool|mixed|null
	 */
	public static function get_background_color_by_name( $color_name ) {
		return Customize_Settings::get_setting( $color_name );
	}

	/**
	 * Get title color on background.
	 *
	 * @param string $color_name The name of the color.
	 * @param string $background_color_name The background color name of the color.
	 *
	 * @return bool|mixed|string|null
	 */
	public static function get_text_color_on_background( $color_name, $background_color_name ) {
		$primary_color = Customize_Settings::get_setting( $color_name );

		if ( ! $background_color_name ) {
			$background_color_name = 'wb4wp_color_section_background_setting';
		}

		$background_color = Customize_Settings::get_setting( $background_color_name );

		if ( self::calculate_luminosity_ratio( $background_color, $primary_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary_color;
		}

		// Fallbacks.
		$primary_color  = Customize_Settings::get_setting( 'wb4wp_color_section_accent1_setting' );
		$primary2_color = Customize_Settings::get_setting( 'wb4wp_color_section_accent2_setting' );
		$text_color     = Customize_Settings::get_setting( 'wb4wp_color_section_text_setting' );

		if ( self::calculate_luminosity_ratio( $background_color, $primary_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, $primary2_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary2_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, $text_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $text_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, self::COLOR_BLACK ) > 3.5 ) {
			return self::COLOR_BLACK;
		} else {
			return self::COLOR_WHITE;
		}
	}

	public static function get_text_color_stronger() {
		$text_color = self::get_text_color_on_background( 'wb4wp_color_section_text_setting', null );

		return self::adjust_brightness( $text_color, -0.2 );
	}

	public static function get_text_color_with_opacity( $opacity ) {
		$text_color = self::get_text_color_on_background( 'wb4wp_color_section_text_setting', null );

		return self::set_opacity_in_color( $text_color, $opacity );
	}

	/**
	 * Get text color for color.
	 *
	 * @param string $color hex color.
	 *
	 * @return string
	 */
	public static function get_text_color_for_color( $color ) {
		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $color ) > 0.3 ) {
			// 10% color is light so text must be black
			return self::COLOR_BLACK;
		} else {
			// 10% lighter
			return self::COLOR_WHITE;
		}
	}

	/**
	 * Get Primary color of settings and avalulate if color is correct.
	 */
	public static function get_primary_color() {
		$background_color = Customize_Settings::get_setting( 'wb4wp_color_section_background_setting' );
		$primary_color    = Customize_Settings::get_setting( 'wb4wp_color_section_accent1_setting' );
		$primary2_color   = Customize_Settings::get_setting( 'wb4wp_color_section_accent2_setting' );
		$text_color       = Customize_Settings::get_setting( 'wb4wp_color_section_text_setting' );

		if ( self::calculate_luminosity_ratio( $background_color, $primary_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, $primary2_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary2_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, $text_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $text_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, self::COLOR_BLACK ) > 0.5 ) {
			return self::COLOR_BLACK;
		} else {
			return self::COLOR_WHITE;
		}
	}

	/**
	 * Get Primary color of settings and avalulate if color is correct.
	 */
	public static function get_text_color() {
		$background_color = Customize_Settings::get_setting( 'wb4wp_color_section_background_setting' );
		$text_color       = Customize_Settings::get_setting( 'wb4wp_color_section_text_setting' );
		$primary_color    = Customize_Settings::get_setting( 'wb4wp_color_section_accent1_setting' );
		$primary2_color   = Customize_Settings::get_setting( 'wb4wp_color_section_accent2_setting' );

		if ( self::calculate_luminosity_ratio( $background_color, $text_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $text_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, $primary_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, $primary2_color ) >= self::MIN_CONTRAST_RATIO ) {
			return $primary2_color;
		} elseif ( self::calculate_luminosity_ratio( $background_color, self::COLOR_BLACK ) > 0.5 ) {
			return self::COLOR_BLACK;
		} else {
			return self::COLOR_WHITE;
		}
	}

	/**
	 * Get soft version of text color.
	 *
	 * @param string $text_color hex color.
	 *
	 * @return string
	 */
	public static function get_color_softer( $text_color ) {
		if ( null === $text_color ) {
			$text_color = self::get_text_color();
		}

		return self::set_opacity_in_color( $text_color, 0.75 );
	}

	/**
	 * Get stronger version colors.
	 *
	 * @param string $text_color hex color.
	 *
	 * @return string
	 */
	public static function get_color_stronger( $text_color ) {
		if ( null === $text_color ) {
			$text_color = self::get_text_color();
		}

		return self::adjust_brightness( $text_color, -0.10 );
	}

	/**
	 * Get primary color text.
	 */
	public static function get_primary_color_text() {
		$primary_color = self::get_primary_color();

		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $primary_color ) > 0.3 ) {
			// 10% color is light so text must be black
			return self::COLOR_BLACK;
		} else {
			// 10% lighter
			return self::COLOR_WHITE;
		}
	}

	/**
	 * Get primary border color
	 */
	public static function get_primary_color_border() {
		$primary_color = self::get_primary_color();

		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $primary_color ) > 0.3 ) {
			// 10% color is light so text must be black
			return 'rgba(0,0,0,0.1)';
		} else {
			// 10% lighter
			return 'rgb(255,255,255,.1)';
		}
	}

	/**
	 * Get strong background color.
	 */
	public static function get_background_color_strong() {
		$background_color = Customize_Settings::get_setting( 'wb4wp_color_section_background_setting' );

		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $background_color ) > 0.5 ) {
			// 10% DARKER
			return self::adjust_brightness( $background_color, -0.10 );
		} else {
			// 10% lighter
			return self::adjust_brightness( $background_color, 0.10 );
		}
	}

	/**
	 * Get strong background color.
	 */
	public static function get_background_color_stronger() {
		$background_color = Customize_Settings::get_setting( 'wb4wp_color_section_background_setting' );

		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $background_color ) > 0.5 ) {
			// 10% DARKER
			return self::adjust_brightness( $background_color, -0.20 );
		} else {
			// 10% lighter
			return self::adjust_brightness( $background_color, 0.20 );
		}
	}

	/**
	 * Get lighter color of background.
	 */
	public static function get_background_color_lighter() {
		$primary_color = Customize_Settings::get_setting( 'wb4wp_color_section_background_setting' );

		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $primary_color ) < 0.5 ) {
			// 10% DARKER
			return self::adjust_brightness( $primary_color, -0.10 );
		} else {
			// 10% lighter
			return self::adjust_brightness( $primary_color, 0.10 );
		}
	}

	/**
	 * Get stronger version of the primary color.
	 */
	public static function get_primary_color_stronger() {
		$primary_color = self::get_primary_color();

		if ( self::calculate_luminosity( $primary_color ) > 0.3 ) {
			// 10% DARKER
			$primary_color_darker = self::adjust_brightness( $primary_color, -0.28 );

			if ( self::calculate_luminosity_ratio( self::COLOR_BLACK, $primary_color_darker ) >= self::MIN_CONTRAST_RATIO ) {
				return $primary_color_darker;
			}

			return self::COLOR_WHITE;
		} else {
			// 10% lighter
			$primary_color_lighter = self::adjust_brightness( $primary_color, 0.20 );

			if ( self::calculate_luminosity_ratio( self::COLOR_WHITE, $primary_color_lighter ) <= self::MIN_CONTRAST_RATIO ) {
				return $primary_color_lighter;
			}

			return self::COLOR_BLACK;
		}
	}

	/**
	 * Get light version of the primary color.
	 */
	public static function get_primary_color_light() {
		$primary_color = self::get_primary_color();

		// > 0.5 == color is more white then black. so we need to make it "darker"
		if ( self::calculate_luminosity( $primary_color ) > 0.5 ) {
			// 10% DARKER
			return self::adjust_brightness( $primary_color, -0.10 );
		} else {
			// 10% lighter
			return self::adjust_brightness( $primary_color, 0.10 );
		}
	}

	/**
	 * Get lighter version of the primary color.
	 */
	public static function get_primary_color_lighter() {
		$primary_color = self::get_primary_color();

		if ( self::calculate_luminosity( $primary_color ) > 0.5 ) {
			// 20% DARKER
			return self::adjust_brightness( $primary_color, -0.20 );
		} else {
			// 20% lighter
			return self::adjust_brightness( $primary_color, 0.20 );
		}
	}

	/**
	 * Get lightest version of the primary color.
	 */
	public static function get_primary_color_lightest() {
		$primary_color = self::get_primary_color();

		if ( self::calculate_luminosity( $primary_color ) > 0.5 ) {
			// 20% DARKER
			return self::adjust_brightness( $primary_color, -0.50 );
		} else {
			// 20% lighter
			return self::adjust_brightness( $primary_color, 0.50 );
		}
	}

	/**
	 * Get general color for borders
	 */
	public static function get_border_color() {
		$background_color = self::get_text_color_on_background( 'wb4wp_color_section_text_setting', 'wb4wp_color_section_background_setting' );
		return self::set_opacity_in_color( $background_color, 0.1 );
	}

	/**
	 * Increases or decreases the brightness of a color by a percentage of the current brightness.
	 *
	 * @param string $hex_code        Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`.
	 * @param float  $adjust_percent  A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
	 *
	 * @return string
	 */
	public static function adjust_brightness( $hex_code, $adjust_percent ) {
		$hex_code = ltrim( $hex_code, '#' );

		if ( strlen( $hex_code ) === 3 ) {
			$hex_code = $hex_code[0] . $hex_code[0] . $hex_code[1] . $hex_code[1] . $hex_code[2] . $hex_code[2];
		}

		$hex_code = array_map( 'hexdec', str_split( $hex_code, 2 ) );

		foreach ( $hex_code as & $color ) {
			$adjustable_limit = $adjust_percent < 0 ? $color : 255 - $color;
			$adjust_amount    = ceil( $adjustable_limit * $adjust_percent );

			$color = str_pad( dechex( $color + $adjust_amount ), 2, '0', STR_PAD_LEFT );
		}

		return '#' . implode( $hex_code );
	}

	/**
	 * Calculates the luminosity of an given RGB color
	 * the color code must be in the format of RRGGBB
	 * the luminosity equations are from the WCAG 2 requirements
	 * http://www.w3.org/TR/WCAG20/#relativeluminancedef
	 *
	 * @param string $color in hex.
	 * @return float luminosity ratio.
	 */
	private static function calculate_luminosity( $color ) {
		// remove hashtag.
		$color = ltrim( $color, '#' );

		$r = hexdec( substr( $color, 0, 2 ) ) / 255; // red value.
		$g = hexdec( substr( $color, 2, 2 ) ) / 255; // green value.
		$b = hexdec( substr( $color, 4, 2 ) ) / 255; // blue value.
		if ( $r <= 0.03928 ) {
			$r = $r / 12.92;
		} else {
			$r = pow( ( ( $r + 0.055 ) / 1.055 ), 2.4 );
		}

		if ( $g <= 0.03928 ) {
			$g = $g / 12.92;
		} else {
			$g = pow( ( ( $g + 0.055 ) / 1.055 ), 2.4 );
		}

		if ( $b <= 0.03928 ) {
			$b = $b / 12.92;
		} else {
			$b = pow( ( ( $b + 0.055 ) / 1.055 ), 2.4 );
		}
		// calc luminosity.
		return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
	}

	/**
	 * Calculates the luminosity ratio of two colors.
	 * the luminosity ratio equations are from the WCAG 2 requirements
	 * http://www.w3.org/TR/WCAG20/#contrast-ratiodef
	 *
	 * @param string $color1 in hex.
	 * @param string $color2 in hex.
	 * @return float luminosity ratio.
	 */
	private static function calculate_luminosity_ratio( $color1, $color2 ) {
		$l1 = self::calculate_luminosity( $color1 );
		$l2 = self::calculate_luminosity( $color2 );

		if ( $l1 > $l2 ) {
			$ratio = ( ( $l1 + 0.05 ) / ( $l2 + 0.05 ) );
		} else {
			$ratio = ( ( $l2 + 0.05 ) / ( $l1 + 0.05 ) );
		}
		return $ratio;
	}

	/**
	 * Convert hexdec color string to rgb(a) string
	 *
	 * If we want make opacity, we have to convert hexadecimal into rgb(a), because WordPress customizer give to us hexadecimal colour
	 *
	 * @param string $color hex color.
	 * @param float  $opacity opacity between 0 and 1.
	 *
	 * @return string
	 */
	public static function set_opacity_in_color( $color, $opacity = null ) {
		$default = 'rgb( 0, 0, 0 )';

		/**
		 * Return default if no color provided
		 */
		if ( empty( $color ) ) {
			return $default;
		}
		$color = ltrim( $color, '#' );

		/**
		 * Check if color has 6 or 3 characters and get values
		 */
		if ( strlen( $color ) === 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) === 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		$rgb = array_map( 'hexdec', $hex );

		/**
		 * Check if opacity is set(rgba or rgb)
		 */
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}

			$output = 'rgba( ' . implode( ',', $rgb ) . ',' . $opacity . ' )';
		} else {
			$output = 'rgb( ' . implode( ',', $rgb ) . ' )';
		}

		/**
		 * Return rgb(a) color string
		 */
		return $output;
	}

}
