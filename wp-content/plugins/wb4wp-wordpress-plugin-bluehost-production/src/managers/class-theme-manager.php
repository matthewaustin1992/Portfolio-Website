<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Constants\Theme_Constants;
use ZipArchive;

/**
 * Class Theme_Manager
 * @package Wb4Wp\Managers
 */
final class Theme_Manager {

	public static function add_hooks() {
		self::add_transient_update_filter();
		self::add_auto_update_themes_filter();
	}

	/**
	 * Adds the theme to the transient update filter for themes that have the auto update system, but not the theme
	 * parameter.
	 * Theme auto updates were added in build 657 and fixed in build 997.
	 */
	private static function add_transient_update_filter() {
		$current_theme_name = get_stylesheet();
		$wb4wp_theme_name   = self::get_wb4wp_theme_name( $current_theme_name );
		if ( empty( $wb4wp_theme_name ) ) {
			return;
		}

		$theme_version = self::get_current_theme_version();
		if ( $theme_version >= 657 && $theme_version < 997 ) {
			add_filter(
				'site_transient_update_themes',
				function ( $transient_update_themes ) use ( $current_theme_name, $wb4wp_theme_name ) {
					$transient_update_themes->response[ $current_theme_name ]['theme'] = $wb4wp_theme_name;
				}
			);
		}
	}

	/**
	 * De-obfuscate our theme name so WordPress understands that our theme should be updated.
	 */
	private static function add_auto_update_themes_filter() {
		add_filter(
			'option_auto_update_themes',
			function ( $auto_update_themes ) {
				foreach ( $auto_update_themes as $theme ) {
					$wb4wp_theme_name = self::get_wb4wp_theme_name( $theme );
					if ( empty( $wb4wp_theme_name ) ) {
						continue;
					}
					$auto_update_themes[] = $wb4wp_theme_name;
				}

				return $auto_update_themes;
			}
		);
	}

	/**
	 * @param string $current_theme_name
	 *
	 * @return string|false
	 */
	private static function get_wb4wp_theme_name( $current_theme_name ) {
		preg_match( '/(?<=\d-)?wb4wp-wordpress-theme-(?:latest|qa|uat|production)/', $current_theme_name, $matches );

		if ( empty( $matches ) ) {
			return false;
		}

		return $matches[0];
	}

	/**
	 * Installs and enables the
	 * latest wb4wp theme
	 */
	public static function install_theme() {
		try {
			// 1) Get Info from the latest theme out there
			$new_theme = self::get_most_recent_theme();

			// 2) Make sure that we got a valid response from our server
			if ( empty( $new_theme->version ) ) {
				return null;
			}

			// 3 Check if our theme exists in the theme directory
			$our_theme_is_installed = false;
			$installed_themes       = wp_get_themes();

			foreach ( $installed_themes as $theme ) {
				// Find our theme.
				if ( strpos( $theme->stylesheet, Theme_Constants::THEME_NAME ) !== false ) {
					// make the fact that we already have the theme installed.
					$our_theme_is_installed = true;

					if ( wp_get_theme()->stylesheet !== $theme->stylesheet ) {
						self::activate_theme( $theme->stylesheet );
					}
				}
			}

			if ( false === $our_theme_is_installed ) {
				if ( self::install_the_version_of_our_theme( $new_theme ) ) {
					self::activate_theme( self::get_new_theme_name( $new_theme ) );
				}
			}
		} catch ( Exception $e ) {
			Raygun_Manager::get_instance()->exception_handler( $e );
		}
	}

	/**
	 * Retrieves the most recent theme version
	 *
	 * @return string
	 */
	public static function get_most_recent_theme() {
		$json = wp_remote_get( env( 'WB4WP_THEME_VERSION' ) );

		if ( is_wp_error( $json ) ) {
			return null;
		}

		return json_decode( $json['body'] );
	}

	/**
	 * Activate the new theme
	 *
	 * @param $new_theme_name
	 */
	private static function activate_theme( $new_theme_name ) {
		switch_theme( $new_theme_name );
	}

	private static function install_the_version_of_our_theme( $new_theme ) {
		// 4 Let's to update to the new theme
		// 4 a) Download the new zip, store in a dist folder
		// 4 b) Unzip the new zip
		// 4 c) activate the new theme
		// 4 d) remove the old theme.

		$zip_destination = get_theme_root() . '/wb4wp-wordpress-theme.zip';

		// Remove the file from the previous download.
		if ( file_exists( $zip_destination ) ) {
			unlink( $zip_destination );
		}

		// 4a)
		$copy = copy(
			$new_theme->url,
			$zip_destination
		);

		if ( ! $copy ) {
			return false;
		}

		$new_theme_name           = self::get_new_theme_name( $new_theme );
		$theme_folder_destination = trailingslashit( get_theme_root() . '/' . $new_theme_name );

		// 4b)
		if ( file_exists( $theme_folder_destination ) && is_dir( $theme_folder_destination ) ) {
			self::del_tree( $theme_folder_destination );
		}

		$zip = new ZipArchive();
		if ( $zip->open( $zip_destination ) === true ) {
			$zip->extractTo( $theme_folder_destination );
			$zip->close();
		}

		// 5) Clean up zip we just downloaded
		if ( file_exists( $zip_destination ) ) {
			unlink( $zip_destination );
		}

		// return the folder of the new theme.
		return $theme_folder_destination;
	}

	/**
	 * Get the new theme name
	 *
	 * @param $new_theme
	 *
	 * @return string
	 */
	public static function get_new_theme_name( $new_theme ) {
		return 'wb4wp-wordpress-theme-' . str_replace( '.', '-', $new_theme->version );
	}

	/**
	 * Removes a directory and
	 * its contents
	 *
	 * @param $dir
	 *
	 * @return bool
	 */
	private static function del_tree( $dir ) {
		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			( is_dir( "$dir/$file" ) ) ? self::del_tree( "$dir/$file" ) : unlink( "$dir/$file" );
		}
		return rmdir( $dir );
	}

	/**
	 * Returns the version of the current theme
	 * if the active theme is ours
	 *
	 * @return array|false|string|null
	 */
	public static function get_current_theme_version() {
		$current_theme = wp_get_theme();

		if ( strpos( $current_theme->stylesheet, Theme_Constants::THEME_NAME ) !== false ) {
			return $current_theme->get( 'Version' );
		}

		return null;
	}

	/**
	 * Returns the slug of the current theme.
	 *
	 * @return string|null
	 */
	public static function get_current_theme_slug() {
		return get_stylesheet();
	}

	/**
	 * Returns the name of the current theme
	 * Note: this function does not require it to be our theme
	 *
	 * @return array|false|string
	 */
	public static function get_current_theme_name() {
		$current_theme = wp_get_theme();

		return $current_theme->get( 'Name' );
	}
}
