<?php

namespace Wb4Wp\Managers;

use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;

/**
 * Class Provider_Wb4wp_Manager
 * @package Wb4Wp\Managers
 */
final class Provider_Wb4wp_Manager {

	/**
	 * Get Onboarding url
	 *
	 * @return string
	 */
	public static function get_signup_url() {
		$redirect_url   = Environment_Helper::get_app_gateway_url( 'wordpress/v1.0/instances/open?clearSession=true&redirectUrl=' );
		$onboarding_url = Environment_Helper::get_app_url(
			'wb4wp/signup?wp_site_name=' . rawurlencode( get_bloginfo( 'name' ) )
			. '&wp_site_uuid=' . Options_Helper::get_instance_uuid()
			. '&wp_handshake=' . Options_Helper::get_handshake_token()
			. '&wp_callback_url=' . rawurlencode( get_bloginfo( 'wpurl' ) . '?rest_route=/wb4wp/v1/callback' )
			. '&wp_instance_url=' . rawurlencode( get_bloginfo( 'wpurl' ) )
			. '&wp_version=' . get_bloginfo( 'version' )
			. '&completed_onboarding=' . ( ! empty( Options_Helper::get( WB4WP_ONBOARDING_ID ) ) ? 'true' : 'false' )
			. '&plugin_version=' . env( 'WB4WP_PLUGIN_VERSION' )
		);

		return $redirect_url . rawurlencode( $onboarding_url );
	}
}
