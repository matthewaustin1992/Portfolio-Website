<?php

namespace Wb4WpTheme\Managers;

use Wb4WpTheme\Managers\Customize\Customize_Settings;

/**
 * Manages the templates
 * Class Template_Manager
 *
 * @package Wb4WpTheme\Managers
 */
final class Template_Manager {

	const HEADER_TEMPLATE = 'wb4wp_header_section';
	const FOOTER_TEMPLATE = 'wb4wp_footer_section';

	/**
	 * Returns the selected header
	 * the user or a layout provided in $layout
	 *
	 * @param string $setting_name Name of the setting.
	 * @return mixed|null
	 */
	public static function get_header_layout_name( $setting_name ) {
		return self::get_layout_or_get_default(
			$setting_name,
			array(
				'navigation-1',
				'navigation-2',
				'navigation-3',
				'navigation-4',
				'navigation-5',
			)
		);
	}
	/**
	 * Returns the selected header by
	 * the user or a layout provided in $layout
	 *
	 * @return mixed|null
	 */
	public static function get_header() {
		$layout = self::get_header_layout_name( self::HEADER_TEMPLATE );

		$template       = self::get_layout( $layout );
		$meta_data      = get_option( 'wb4wp_metadata', null );
		$global_binding = get_option( 'wb4wp_global_binding', null );

		return self::render(
			$template,
			array(
				'meta_data'      => $meta_data,
				'global_binding' => $global_binding,
				'theme_options'  => array(),
			)
		);
	}

	/**
	 * Get footer layout name
	 *
	 * @param string $setting_name name of the setting.
	 * @return array
	 */
	public static function get_footer_layout_name( $setting_name ) {
		return self::get_layout_or_get_default(
			$setting_name,
			array(
				'footer-1',
				'footer-2',
				'footer-3',
				'footer-4',
				'footer-5',
			)
		);
	}

	/**
	 * Returns the selected footer by
	 * the user or a layout provided in $layout
	 *
	 * @return mixed|null
	 */
	public static function get_footer() {
		$layout = self::get_footer_layout_name( self::FOOTER_TEMPLATE );

		$template       = self::get_layout( $layout );
		$meta_data      = get_option( 'wb4wp_metadata', null );
		$global_binding = get_option( 'wb4wp_global_binding', null );

		return self::render(
			$template,
			array(
				'meta_data'      => $meta_data,
				'global_binding' => $global_binding,
				'theme_options'  => array(),
			)
		);
	}

	/**
	 * Get layout or the default setting.
	 *
	 * @param string $setting_name name of the setting.
	 * @param array  $map .
	 * @return string layout name
	 */
	private static function get_layout_or_get_default( $setting_name, $map ) {
		$layout = Customize_Settings::get_setting( $setting_name . '_layout_setting' );

		if ( ! in_array( $layout, $map, true ) ) {
			$layout = $map[0];
		}

		return $layout;
	}

	/**
	 * Render the template assets.
	 */
	public static function render_template_assets() {

		$header_layout_name = self::get_header_layout_name( self::HEADER_TEMPLATE );
		$footer_layout_name = self::get_footer_layout_name( self::FOOTER_TEMPLATE );

		self::render_asset_by_template( $header_layout_name );
		self::render_asset_by_template( $footer_layout_name );
	}

	/**
	 * Retrieves the version of the theme.
	 */
	public static function get_theme_version() {
		$theme = wp_get_theme();

		return $theme->version;
	}

	/**
	 * Renders the assets by the layout name.
	 *
	 * @param string $layout_name the filename if the asset located in the dist folder.
	 */
	private static function render_asset_by_template( $layout_name ) {
		$style_path = '/dist/' . $layout_name . '/' . $layout_name . '.css';
		$has_style  = file_exists( __dir__ . '/../..' . $style_path );
		$version    = self::get_theme_version();

		if ( $has_style ) {
			wp_enqueue_style( $layout_name, get_template_directory_uri() . $style_path, array(), $version );
		}

		$script_path = '/dist/' . $layout_name . '/' . $layout_name . '.js';
		$has_script  = file_exists( __dir__ . '/../..' . $script_path );

		if ( $has_script ) {
			wp_enqueue_script( $layout_name, get_template_directory_uri() . $script_path, array(), $version, true );
		}
	}

	/**
	 * Generates the layout path
	 * and returns it
	 *
	 * @param string $file_name Name of the file.
	 *
	 * @return string
	 */
	private static function get_layout( $file_name ) {
		$template_dir = get_template_directory();
		return "{$template_dir}/dist/{$file_name}/{$file_name}.php";
	}

	/**
	 * Renders the given template.
	 *
	 * @param string $template path of the template.
	 * @param array  $variables .
	 * @return mixed
	 */
	private static function render( $template, $variables = array() ) {
		$output = '';

		if ( file_exists( $template ) ) {
			// @codingStandardsIgnoreStart
			extract( $variables );
			// @codingStandardsIgnoreEnd

			ob_start();

			include_once $template;

			$output = ob_get_clean();
		}

		return $output;
	}

}
