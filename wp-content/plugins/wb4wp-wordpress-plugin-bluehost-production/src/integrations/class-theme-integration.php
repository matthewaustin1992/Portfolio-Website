<?php

namespace Wb4Wp\Integrations;

use Wb4Wp\Integrations\Exceptions\Theme_Does_Not_Support_Setting_Querying_Exception;
use Wb4Wp\Integrations\Exceptions\Theme_Not_Active_Exception;

/**
 * Class Theme_Integration
 * @package Wb4Wp\Integrations
 */
class Theme_Integration {

	private static $customize_settings_class_names = array(
		'\Wb4WpTheme\Managers\Customize\Customize_Settings',
		'\Wb4WpTheme\Managers\Customize\CustomizeSettings',
	);

	private static $customize_controls_class_names = array(
		'\Wb4WpTheme\Managers\Customize\Customize_Controls',
		'\Wb4WpTheme\Managers\Customize\CustomizeControls',
	);

	/**
	 * Retrieves the value of a customize setting from the theme.
	 *
	 * @param string $setting_name .
	 *
	 * @return mixed
	 * @throws Theme_Not_Active_Exception
	 */
	public static function get_customize_setting( $setting_name ) {
		$customize_settings_class_name = current(
			array_filter(
				self::$customize_settings_class_names,
				function ( $class_name ) {
					return class_exists( $class_name );
				}
			)
		);

		if ( false === $customize_settings_class_name || ! method_exists( $customize_settings_class_name, 'get_setting' ) ) {
			throw new Theme_Not_Active_Exception();
		}

		return call_user_func( array( $customize_settings_class_name, 'get_setting' ), $setting_name );
	}

	/**
	 * @param string|null $page_type
	 *
	 * @return mixed
	 * @throws Theme_Not_Active_Exception
	 * @throws Theme_Does_Not_Support_Setting_Querying_Exception
	 */
	public static function get_customize_setting_list( $page_type = null ) {
		$customize_settings_class_name = current(
			array_filter(
				self::$customize_settings_class_names,
				function ( $class_name ) {
					return class_exists( $class_name );
				}
			)
		);

		if ( false === $customize_settings_class_name ) {
			throw new Theme_Not_Active_Exception();
		}

		if ( ! method_exists( $customize_settings_class_name, 'get_setting_list' ) ) {
			throw new Theme_Does_Not_Support_Setting_Querying_Exception();
		}

		return call_user_func( array( $customize_settings_class_name, 'get_setting_list' ), $page_type );
	}

	/**
	 * Constructs the full setting name based on the section and setting names.
	 *
	 * @param string $section_name .
	 * @param string $setting_name .
	 *
	 * @return string
	 * @throws Theme_Not_Active_Exception
	 */
	public static function get_full_setting_name( $section_name, $setting_name ) {
		$customize_controls_class_name = current(
			array_filter(
				self::$customize_controls_class_names,
				function ( $class_name ) {
					return class_exists( $class_name );
				}
			)
		);

		if (
			false === $customize_controls_class_name
			|| ! method_exists( $customize_controls_class_name, 'get_full_section_name' )
			|| ! method_exists( $customize_controls_class_name, 'get_full_setting_name' )
		) {
			throw new Theme_Not_Active_Exception();
		}

		$full_section_name = call_user_func( array( $customize_controls_class_name, 'get_full_section_name' ), $section_name );
		$full_setting_name = call_user_func( array( $customize_controls_class_name, 'get_full_setting_name' ), $full_section_name, $setting_name );

		return $full_setting_name;
	}

}
