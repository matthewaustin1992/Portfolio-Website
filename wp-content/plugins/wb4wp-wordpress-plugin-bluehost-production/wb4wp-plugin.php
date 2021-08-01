<?php

/**
 * Main plugin file for WB4WP
 *
 * @file    wb4wp-plugin.php
 * @package WB4WP
 */

use Wb4Wp\Constants\Provider_Names;
use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Managers\Raygun_Manager;
use Wb4Wp\Managers\Theme_Manager;
use Wb4Wp\Wb4wp;

/**
 * Plugin Name: Bluehost Website Builder
 * Plugin URI: https://www.newfold.com
 * Description:
 * Author: Newfold Digital
 * Version: 1.0-2335 : 20210616_092004
 * Author URI: https://www.newfold.com
 */

/**
 * Swagger Init
 *
 * @OA\Info (title="WP Edit Plugin API", version="1.0-2335")
 *
 * @OA\Server (
 *  url="{YOUR_DOMAIN}/?rest_route=",
 * )
 *
 * @OA\Tag (
 *  name="Onboarding",
 *  description="Routes related to the onboarding"
 * )
 *
 * @OA\Tag (
 *  name="Publishing",
 *  description="Routes related to publishing"
 * )
 *
 * @OA\Tag (
 *  name="Templates",
 *  description="Routes related to templating"
 * )
 *
 * @OA\Tag (
 *  name="Other",
 * )
 */

// Make sure we don't expose any info if called directly.
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there! I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// SonarQube code smells.
const SECTIONS     = 'sections';
const DEPENDENCIES = 'dependencies';
const VERSION      = 'version';

/**
 * Loads the plugin
 *
 * @return bool
 *
 * @throws Exception
 */
function _load_wb4wp_plugin() {
	global $wb4wp_instance;

	if ( null !== $wb4wp_instance ) {
		return true;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	try {
		define( 'WB4WP_PLUGIN_DIR', __DIR__ . '/' );
		define( 'WB4WP_PLUGIN_DIR_NAME', basename( __DIR__ ) );
		define( 'WB4WP_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'WB4WP_PLUGIN_FILE', __FILE__ );
		define( 'WB4WP_PLUGIN_VERSION', get_plugin_data( __FILE__ )['Version'] );
		define( 'WB4WP_INSTANCE_HANDSHAKE_TOKEN', 'wb4wp_handshake_token' );
		define( 'WB4WP_INSTANCE_HANDSHAKE_EXPIRATION', 'wb4wp_handshake_expiration' );
		define( 'WB4WP_ENCRYPTION_KEY_KEY', 'wb4wp_encryption_key' );
		define( 'WB4WP_INSTANCE_API_KEY_KEY', 'wb4wp_instance_api_key' );
		define( 'WB4WP_INSTANCE_ID_KEY', 'wb4wp_instance_id' );
		define( 'WB4WP_INSTANCE_UUID_KEY', 'wb4wp_instance_uuid' );
		define( 'WB4WP_SSO_JWT_TOKEN', 'wb4wp_sso_jwt_token' );
		define( 'WB4WP_SSO_JWT_TTL', 'wb4wp_sso_jwt_ttl' );
		define( 'WB4WP_CONNECTED_ACCOUNT_ID', 'wb4wp_connected_account_id' );
		define( 'WB4WP_SITE_ID', 'wb4wp_site_id' );
		define( 'WB4WP_ONBOARDING_ID', 'wb4wp_onboarding_id' );
		define( 'WB4WP_PLUGIN_DIR_NAME_KEY', 'wb4wp_plugin_dir_name' );
		define( 'WB4WP_JWT', 'wb4wp_jwt_token' );
		define( 'WB4WP_STATS', 'wb4wp_stats' );
		define( 'WB4WP_ENVIRONMENT', 'wb4wp_environment' );
		define( 'WB4WP_PROVIDER', 'wb4wp_provider' );
		define( 'WB4WP_PROVIDER_VAL', '{PROVIDER}' );
		define( 'WB4WP_PLUGIN_STATE', 'wb4wp_plugin_state' );
		define( 'WB4WP_LANDER_TYPE', 'wb4wp_lander_type' );
		define( 'WB4WP_WP_TEXT_DOMAIN', 'wb4wp' );
		define( 'WB4WP_THEME_MODS', 'theme_mods_bluehost-theme' );
		define( 'WB4WP_APPEARANCE', 'wb4wp_appearance' );
		define( 'WB4WP_SITE_LANGUAGE', 'wb4wp_site_language' );
		define( 'WB4WP_SITE_TITLE', 'wb4wp_site_title' );
		define( 'WB4WP_SITE_DESCRIPTION', 'wb4wp_site_description' );
		define( 'WB4WP_SITE_FAVICON', 'wb4wp_site_favicon' );
		define( 'WB4WP_GLOBAL_SECTIONS', 'wb4wp_global_sections' );
		define( 'WB4WP_THEME', 'wb4wp_theme' );
		define( 'WB4WP_METADATA', 'wb4wp_metadata' );
		define( 'WB4WP_GLOBAL_BINDING', 'wb4wp_global_binding' );
		define( 'WB4WP_API_KEYS', 'wb4wp_api_keys' );
		define( 'WB4WP_URLS', 'wb4wp_urls' );
		define( 'WB4WP_FEATURE_STORAGE', 'wb4wp_feature_storage' );
		define( 'WB4WP_GLOBAL_FEATURE_MODEL', 'wb4wp_global_feature_model' );
		define( 'WB4WP_HEADER_SECTION', 'wb4wp_header_section' );
		define( 'WB4WP_MENU_NAME', 'WB4WP Menu' );
		define( 'WB4WP_THEME_UPDATE_CRON', 'wb4wp_theme_update_cron' );
		define( 'WB4WP_PLUGIN_UPDATE_CRON', 'wb4wp_plugin_update_cron' );
		define( 'WB4WP_MIGRATIONS', 'wb4wp_migrations' );

		if ( ! defined( 'CE4WP_REFERRED_BY' ) ) {
			define( 'CE4WP_REFERRED_BY', 'ce4wp_referred_by' );
		}

		// Load all the required files.
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require_once __DIR__ . '/vendor/autoload.php';
		}

		if ( Environment_Helper::is_test_environment() ) {
			// phpcs:disable

			ini_set( 'display_errors', 1 );
			ini_set( 'display_startup_errors', 1 );
			error_reporting( E_ALL );

			// phpcs:enable
		}

		// Dynamically set the Provider name, based on the wb4wp-provider value.
		if ( WB4WP_PROVIDER_VAL !== '{PROVIDER}' ) {
			Options_Helper::set( WB4WP_PROVIDER, WB4WP_PROVIDER_VAL );
		}
		$provider_name = Provider_Helper::get_provider_name();
		define( 'WB4WP_PLUGIN_NAME', wb4wp_get_plugin_name( $provider_name ) );

		// Start the plugin.
		$wb4wp_instance = Wb4wp::get_instance();
		$wb4wp_instance->add_hooks();

		Options_Helper::set( WB4WP_ENVIRONMENT, env( 'WB4WP_ENVIRONMENT' ) );
		Options_Helper::set( WB4WP_PROVIDER, env( 'WB4WP_PROVIDER' ) );
	} catch ( Exception $ex ) {
		Raygun_Manager::get_instance()->exception_handler( $ex );

		throw $ex;
	}

	return true;
}

function wb4wp_get_plugin_name( $provider_name ) {
	switch ( $provider_name ) {
		case Provider_Names::WB4WP:
			return 'WebsiteBuilder';
		case Provider_Names::BLUEHOST:
		case Provider_Names::BLUEHOST_INDIA:
		case Provider_Names::BLUEHOST_ASIA:
			return 'Bluehost Website Builder';
		default:
			return $provider_name;
	}
}

/**
 * On activation of the plugin
 */
function wb4wp_activated() {
	try {
		_load_wb4wp_plugin();
		Theme_Manager::install_theme();
		update_option( WB4WP_PLUGIN_DIR_NAME_KEY, WB4WP_PLUGIN_DIR_NAME );
		Options_Helper::set_plugin_state( 'new' );
	} catch ( Exception $ex ) {
		Raygun_Manager::get_instance()->exception_handler( $ex );
	}
}

/**
 * Deactivation hook.
 */
function wb4wp_deactivated() {
	try {
		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();
		// Unlink all custom metadata
		// OptionsHelper::unlink(); for now we dont want to reset. this is only when we are in the official store
		// Remove auto-update crons.
		wp_clear_scheduled_hook( WB4WP_THEME_UPDATE_CRON );
		wp_clear_scheduled_hook( WB4WP_PLUGIN_UPDATE_CRON );
	} catch ( Exception $ex ) {
		Raygun_Manager::get_instance()->exception_handler( $ex );
	}
}

add_action( 'plugins_loaded', '_load_wb4wp_plugin', 10 );
register_activation_hook( __FILE__, 'wb4wp_activated' );
register_deactivation_hook( __FILE__, 'wb4wp_deactivated' );

function test_provider_status() {
	$current_env = Environment_Helper::get_environment();

	if ( Provider_Helper::is_bluehost() && ( 'dev' === $current_env || 'qa' === $current_env ) ) {
		// phpcs:disable

		$message = sprintf( __( '<strong>WARNING!</strong> Provider Bluehost is not supported on environment "<strong>%1$s</strong>".', WB4WP_WP_TEXT_DOMAIN ), esc_attr( $current_env ) );
		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );

		// phpcs:enable
	}
}
add_action( 'admin_notices', 'test_provider_status' );

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * Passes translations to JavaScript.
 */
function saved_pages_files() {
	$blocks = array(
		'block-generic-wb4wp-content',
		'block-generic-section',
		'container',
	);

	foreach ( $blocks as $block_name ) {
		$asset_file = include_once plugin_dir_path( __FILE__ ) . "build/$block_name.asset.php";

		wp_register_style(
			$block_name,
			plugins_url( 'build/' . $block_name . '.css', __FILE__ ),
			array(),
			$asset_file[ VERSION ]
		);

		wp_register_script(
			$block_name,
			plugins_url( 'build/' . $block_name . '.js', __FILE__ ),
			$asset_file[ DEPENDENCIES ],
			$asset_file[ VERSION ],
			false
		);
	}
}

add_action( 'init', 'saved_pages_files' );

/**
 * Registers custom meta data for posts
 */
function wb4wp__post_meta_data() {
	$meta_keys = array(
		'WB4WB4WP_MODE',
		'WB4WP_PAGE_SCRIPTS',
		'WB4WP_PAGE_STYLES',
		'WB4WP_PAGE_FONTS',
		'WB4WP_PAGE_HEADER',
		'WB4WP_PAGE_FOOTER',
	);

	foreach ( $meta_keys as $meta_key ) {
		register_meta(
			'post',
			$meta_key,
			array(
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}

add_action( 'init', 'wb4wp__post_meta_data' );

/**
 * Extends the timeout of the default http requests
 *
 * @return int
 */
function wb4wp__timeout_extend() {
	// Default timeout is 5.
	return 10;
}

add_filter( 'http_request_timeout', 'wb4wp__timeout_extend' );

