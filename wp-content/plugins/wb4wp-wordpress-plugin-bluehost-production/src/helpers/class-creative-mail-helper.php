<?php

namespace Wb4Wp\Helpers;

/**
 * Class Creative_Mail_Helper
 * @package Wb4Wp\Helpers
 */
final class Creative_Mail_Helper {

	/**
	 * Checks if the Creative Mail plugin is installed in the WP instance.
	 *
	 * @return bool
	 */
	public static function is_creative_mail_installed() {
		$plugins = array_filter(
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			function ( $plugin ) {
				return strpos( $plugin, 'creative-mail-plugin' ) !== false;
			}
		);

		return ! empty( $plugins );
	}

	/**
	 * Checks if CE4WP_REFFERED_BY option already has the BHWB flag
	 *
	 * @return bool
	 */
	public static function has_bhwb_referrer() {
		$referred_by_option = Options_Helper::get( CE4WP_REFERRED_BY, '' );
		$referrer_value     = '';
		if ( is_array( $referred_by_option ) && array_key_exists( 'source', $referred_by_option ) ) {
			$referrer_value = $referred_by_option['source'];
		} elseif ( is_string( $referred_by_option ) ) {
			$referrer_value = $referred_by_option;
		}

		return strpos( $referrer_value, 'BHWB' ) !== false;
	}

	/**
	 * Sets the BHWB flag in the CE4WP_REFFERED_BY option
	 *
	 * @return bool
	 */
	public static function set_bhwb_referrer() {
		$referred_by = Options_Helper::get( CE4WP_REFERRED_BY, '' );
		if ( is_array( $referred_by ) ) {
			if ( strpos( $referred_by['source'], 'BHWB' ) === false ) {
				$source_empty           = empty( $referred_by['source'] );
				$referred_by['source'] .= $source_empty ? 'BHWB' : '_BHWB';
				return Options_Helper::set( CE4WP_REFERRED_BY, $referred_by );
			}
		} else {
			$is_empty     = empty( $referred_by );
			$referred_by .= $is_empty ? 'BHWB' : '_BHWB';
			return Options_Helper::set( CE4WP_REFERRED_BY, $referred_by );
		}
	}
}
