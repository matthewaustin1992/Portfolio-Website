<?php

namespace Wb4Wp\Managers;

use Wb4Wp\Helpers\Post_Helper;
use Wb4Wp\Constants\Environment_Names;
use Wb4Wp\Helpers\Environment_Helper;

/**
 * Class Template_Manager
 * @package Wb4Wp\Managers
 */
final class Template_Manager {

	const THE_CONTENT   = 'the_content';
	const WB4WP_STYLES  = 'ee_styles';
	const WB4WP_SCRIPTS = 'ee_scripts';

	/**
	 * The array of templates that this plugin tracks.
	 *
	 * @var array
	 */
	protected $templates = array();

	public function __construct() {
		global $wp_version;

		if ( version_compare( $wp_version, '5.0.0' ) >= 0 ) {
			$this->register_blocks();
		}
	}

	/**
	 * Register Gutenberg blocks.
	 */
	public function register_blocks() {
		$blocks = array(
			'block-generic-wb4wp-content',
			'block-generic-section',
			'container',
		);

		foreach ( $blocks as $block_name ) {
			register_block_type(
				"wb4wp/$block_name",
				array(
					'style'  => $block_name,
					'editor_style'  => $block_name,
					'editor_script' => $block_name,
				)
			);
		}
	}

	public static function output_essential_scripts_and_json_objects_used_by_express_editor_runtime( $post_id ) {
		// Check if the page is published pre Gutenberg support.
		$asset_file = ABSPATH . 'wp-content/uploads/wb4wp-page-assets/assets_' . $post_id . '.json';
		if ( file_exists( $asset_file ) ) {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$assets    = json_decode( file_get_contents( $asset_file ) );
			$permalink = get_option( 'permalink_structure' );

			$current_env = Environment_Helper::get_environment();
			if ($current_env === Environment_Names::PRODUCTION) {
				echo '<script defer type="text/javascript" src="https://runtime.builderservices.io/runtime-endurance-default/bundle.js"></script>';
			} else {
				echo '<script defer type="text/javascript" src="https://runtime.' . $current_env . '.builderservices.io/runtime-endurance-default/bundle.js"></script>';
			}

			if ( ! empty( $assets->fonts ) ) {
				// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
				echo "<link rel='stylesheet' href='" . $assets->fonts . "'>";
			}

			if ( ! empty( $assets->baseStyle ) ) {
				echo "<style type='text/css'>" . $assets->baseStyle . '</style>';
			}

			if ( ! empty( $assets->siteModel ) ) {
				// Set partnerId to 999 for now. TODO make a useHosting api setting.
				echo '
					<script>
					window._wpId=' . $post_id . ";
					window.websiteBuilder = {
						...(window.websiteBuilder || {}),
						root: '" . site_url( '/' ) . "',
						restApiRoot: '" . untrailingslashit( get_rest_url() ) . "',
						pluginVersion: '" . WB4WP_PLUGIN_VERSION . "',
						themeVersion: '" . \Wb4Wp\Managers\Theme_Manager::get_current_theme_version() . "',
						themeSlug: '" . \Wb4Wp\Managers\Theme_Manager::get_current_theme_slug() . "'
					};
					window._isPublished=true;
					window._site=" . wp_json_encode( $assets->siteModel ) . ';
					window._permaLinkStructure="' . $permalink . '";
					</script>
				';
			}

			if ( ! empty( $assets->featureScript ) ) {
				echo '<script>' . $assets->featureScript . '</script>';
			}

			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}
	}

	/**
	 * Will add all the hooks that are required to setup our plugin templates.
	 */
	public function add_hooks() {
		add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );

		// Add a filter to the save post to inject out template into the page cache.
		add_filter( 'wp_insert_post_data', array( $this, 'register_project_templates' ) );

		// Add a filter to the template include to determine if the page has our
		// template assigned and return its path.
		add_filter( 'template_include', array( $this, 'view_project_template' ) );

		// Add your templates to this array.
		$this->templates = array(
			'wb4wp-template.php' => 'Website Builder Template',
		);

		// Register hook to load styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'ee_enqueue_styles' ) );

		add_action( 'template_redirect', array( $this, 'thisismyurl_change_theme_manually' ) );

		add_action( 'init', array( $this, 'expand_allowed_tags' ) );
	}

	public function expand_allowed_tags() {
		global $allowedposttags;

		if ( empty( $allowedposttags['style'] ) ) {
			// phpcs:ignore
			$allowedposttags['style'] = array(
				'scoped' => true,
			);
		}
	}

	public function thisismyurl_change_theme_manually() {
		if ( 'twentyeleven' !== get_stylesheet() && is_admin() ) {
			switch_theme( 'twentyeleven', 'style.css' );
		}
	}

	public function ee_enqueue_styles() {
		global $post;

		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( null !== $post && Post_Helper::is_built_with_wb4wp( $post->ID ) ) {
			$styles = get_post_meta( $post->ID, 'WB4WP_PAGE_STYLES', true );
			if ( null !== $styles ) {
				wp_register_style( self::WB4WP_STYLES, false, false, WB4WP_PLUGIN_VERSION );
				wp_enqueue_style( self::WB4WP_STYLES );
				wp_add_inline_style( self::WB4WP_STYLES, base64_decode( $styles ) );
			}
		} else {
			$styles = get_option( 'WB4WP_GLOBAL_STYLES', null );
			if ( null !== $styles ) {
				wp_register_style( self::WB4WP_STYLES, false, false, WB4WP_PLUGIN_VERSION );
				wp_enqueue_style( self::WB4WP_STYLES );
				wp_add_inline_style( self::WB4WP_STYLES, base64_decode( $styles ) );
			}
		}

		// phpcs:enable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}

	public function ee_enqueue_scripts() {
		global $post;
		if ( Post_Helper::is_built_with_wb4wp( $post->ID ) ) {
			$scripts = get_post_meta( $post->ID, 'WB4WP_PAGE_SCRIPTS', true );
			if ( null !== $scripts ) {
				wp_register_style( self::WB4WP_SCRIPTS, false, false, WB4WP_PLUGIN_VERSION );
				wp_enqueue_style( self::WB4WP_SCRIPTS );
				// phpcs:ignore
				wp_add_inline_style( self::WB4WP_SCRIPTS, base64_decode( $scripts ) );
			}
		}
	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 * @param $posts_templates
	 *
	 * @return array
	 */
	public function add_new_template( $posts_templates ) {
		return array_merge( $posts_templates, $this->templates );
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doesn't really exist.
	 *
	 * @param $atts
	 *
	 * @return mixed
	 */
	public function register_project_templates( $atts ) {
		// Create the key used for the themes cache.
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array.
		$page_templates = wp_get_theme()->get_page_templates();
		if ( empty( $page_templates ) ) {
			$page_templates = array();
		}

		// New cache, therefore remove the old one.
		wp_cache_delete( $cache_key, 'themes' );

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$page_templates = array_merge( $page_templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates.
		wp_cache_add( $cache_key, $page_templates, 'themes', 1800 );

		return $atts;
	}

	/**
	 * Checks if the template is assigned to the page
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function view_project_template( $template ) {
		// Get global post.
		global $post;

		if ( $post ) {
			$post_meta = get_post_meta( $post->ID, '_wp_page_template', true );

			if ( isset( $this->templates[ $post_meta ] ) ) {
				$file = WB4WP_PLUGIN_DIR . 'src/templates/' . $post_meta;

				// Just to be safe, we check if the file exist first.
				if ( file_exists( $file ) ) {
					return $file;
				}
			}
		}

		/**
		 * Return template if post is empty
		 * or if we don't have a custom one defined
		 */
		return $template;
	}
}
