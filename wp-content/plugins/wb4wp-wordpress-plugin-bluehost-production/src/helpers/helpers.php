<?php

if ( ! function_exists( 'env' ) ) {
	/**
	 * Returns the environment variable
	 *
	 * @param $name
	 * @param null $default_value
	 *
	 * @return mixed|null
	 */
	function env( $name, $default_value = null ) {
		return ! empty( $_ENV[ $name ] ) ? $_ENV[ $name ] : $default_value;
	}
}

if ( ! function_exists( 'add_inline_js_data_object' ) ) {
	function add_inline_js_data_object( $handler, $data ) {
		wp_add_inline_script(
			$handler,
			'window.websiteBuilder = {
                ...(window.websiteBuilder || {}),
                ...' . wp_json_encode( $data ) . '
            }',
			'before'
		);
	}
}

if ( ! class_exists( 'WP_Customize_Manager' ) ) {
	if ( defined( 'ABSPATH' ) ) {
		include_once ABSPATH . 'wp-includes/class-wp-customize-manager.php';
	}
}
