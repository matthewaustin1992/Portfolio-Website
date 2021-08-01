<?php
/**
 * Website Builder Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Website Builder Theme
 */

use Wb4WpTheme\Managers\Blog_Manager;
use Wb4WpTheme\Managers\Customize_Manager;
use Wb4WpTheme\Managers\Template_Manager;
use Wb4WpTheme\Managers\Update_Manager;

if ( ! defined( '_WEBSITE_BUILDER_THEME_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_WEBSITE_BUILDER_THEME_VERSION', '1.0.0' );
}

if ( ! defined( 'WB4WP_THEME_VERSION' ) ) {
	define( 'WB4WP_THEME_VERSION', wp_get_theme()->Version );
}

// Load all the required files.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'WB4WP_PROVIDER_NAME', 'Website Builder' );
define( 'WB4WP_UPDATE_URL', 'https://wp-versions.builderservices.io/content/theme-version.json' );

add_action( 'after_setup_theme', 'wb4wp_theme_setup' );

if ( ! function_exists( 'wb4wp_theme_setup' ) ) :

	/**
	 * Setup WB4WP theme.
	 */
	function wb4wp_theme_setup() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'responsive-embeds' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'wb4wp' => esc_html__( 'Primary', 'wb4wp_theme' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
	}
endif;

if ( ! function_exists( 'storefront_is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation
	 */
	function storefront_is_woocommerce_activated() {
		return class_exists( 'WooCommerce' );
	}
}

if ( ! function_exists( 'jetpack_is_installed_and_activated' ) ) {
	/**
	 * Query Jetpack activation
	 *
	 * @return bool
	 */
	function jetpack_is_installed_and_activated() {
		return class_exists( 'Jetpack' );
	}
}

if ( storefront_is_woocommerce_activated() ) {
	require __DIR__ . '/src/woocommerce/single-product-summary.php';
	require __DIR__ . '/src/woocommerce/shop-loop.php';
	require __DIR__ . '/src/woocommerce/cart-page.php';
	require __DIR__ . '/src/woocommerce/checkout-page.php';
}

/**
 * Enqueue theme style assets.
 */
function wb4wp_theme_enqueue_style() {
	$theme_name = 'wb4wp-theme';
	wp_enqueue_style( $theme_name, get_template_directory_uri() . '/dist/main.css', false, WB4WP_THEME_VERSION );

	if ( have_posts() ) {
		wp_enqueue_style( $theme_name . '-blog', get_template_directory_uri() . '/dist/blog/blog.css', false, WB4WP_THEME_VERSION );
	}

	if ( storefront_is_woocommerce_activated() ) {
		wp_enqueue_script(
			$theme_name . '-woocommerce-js',
			get_template_directory_uri() . '/dist/woocommerce/woocommerce.js',
			array(),
			WB4WP_THEME_VERSION,
			true
		);
		wp_enqueue_style( $theme_name . '-woocommerce', get_template_directory_uri() . '/dist/woocommerce/woocommerce.css', false, WB4WP_THEME_VERSION );
	}

	if ( jetpack_is_installed_and_activated() ) {
		wp_enqueue_style( $theme_name . '-jetpack', get_template_directory_uri() . '/dist/jetpack/jetpack.css', false, WB4WP_THEME_VERSION );
	}

	$template_manager = new Template_Manager();
	$template_manager->render_template_assets( $theme_name );
}

add_action( 'wp_enqueue_scripts', 'wb4wp_theme_enqueue_style' );

/**
 * Initialize the wb4wp customize controls.
 *
 * @param WP_Customize_Manager $wp_customize .
 */
function wb4wp_theme_customizer( $wp_customize ) {
	$customize_manager = new Customize_Manager( $wp_customize );
	$customize_manager->register_controls();

	wp_enqueue_style( 'wb4wp-theme_customize-style', get_template_directory_uri() . '/dist/customize.css', false, WB4WP_THEME_VERSION );
}

add_action( 'customize_register', 'wb4wp_theme_customizer' );

/**
 * Enqueue a script to fix customize checkboxes.
 */
function wb4wp_customize_checkbox_fix() {
	wp_enqueue_script(
		'wb4wp_customize_checkbox_fix',
		get_template_directory_uri() . '/src/customizer/customize-checkbox-fix.js',
		array( 'jquery', 'customize-controls' ),
		WB4WP_THEME_VERSION,
		true
	);
}

add_action( 'customize_controls_enqueue_scripts', 'wb4wp_customize_checkbox_fix' );

/**
 * Enqueue a script to handle customize preview page changes.
 */
function wb4wp_customize_preview_page() {
	$script_handle = 'wb4wp_customize_preview_page';

	wp_enqueue_script(
		$script_handle,
		get_template_directory_uri() . '/src/customizer/customize-preview-page.js',
		array( 'jquery', 'customize-controls' ),
		WB4WP_THEME_VERSION,
		true
	);

	wp_localize_script(
		$script_handle,
		'wb4wpUrls',
		array(
			'base'         => get_site_url(),
			'singlePost'   => Blog_Manager::get_most_recent_post_url(),
			'blogOverview' => Blog_Manager::get_overview_url(),
		)
	);
}

add_action( 'customize_controls_enqueue_scripts', 'wb4wp_customize_preview_page' );

/**
 * Add menu pages.
 */
function wb4wp_add_menu_pages() {
	global $submenu;

	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$submenu['themes.php'][500] = array(
		'Edit ' . WB4WP_PROVIDER_NAME . ' settings',
		'edit_theme_options',
		'admin.php?page=wb4wp-editor&wp-edit-link=/appearance',
	);
}

add_action( 'admin_menu', 'wb4wp_add_menu_pages' );

/**
 * Initialize wb4wp theme.
 */
function wb4wp_theme_init() {
	// This theme support page wide sections.
	add_theme_support( 'align-wide' );

	$update_manager = new Update_Manager();
	$update_manager->check_for_updates();
}

add_action( 'init', 'wb4wp_theme_init' );

/**
 * Enqueue customize JS.
 */
function wb4wp_theme_customizer_js() {
	wp_enqueue_script(
		'wb4wp-theme-customizer-js',
		get_template_directory_uri() . '/src/customizer/customizer.js',
		array( 'jquery' ),
		WB4WP_THEME_VERSION,
		true
	);

	wp_localize_script(
		'wb4wp-theme-customizer-js',
		'wb4wpL10n',
		array(
			// translators: %s = provider name.
			'title_text' => sprintf( __( 'Open %s', 'wb4wp_theme' ), WB4WP_PROVIDER_NAME ),
			'home_url'   => get_site_url(),
		)
	);
}

add_action( 'customize_controls_enqueue_scripts', 'wb4wp_theme_customizer_js' );

function wb4wp_add_to_cart_fragments( $fragments ) {
	global $woocommerce;
	$items      = $woocommerce->cart->get_cart();
	$item_count = 0;

	foreach ( $items as $item ) {
		$item_count += $item['quantity'];
	}

	$fragments['item_count'] = $item_count;

	return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'wb4wp_add_to_cart_fragments', 10, 3 );
