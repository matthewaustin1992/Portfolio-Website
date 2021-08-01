<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Constants\Provider_Names;
use Wb4Wp\Helpers\Creative_Mail_Helper;
use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Post_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Wb4wp;

/**
 * Class Admin_Manager
 * @package Wb4Wp\Managers
 */
final class Admin_Manager {

	public $provider;

	/**
	 * AdminManager constructor.
	 */
	public function __construct() {
		$this->provider = Provider_Helper::get_provider_name();
	}

	/**
	 * Will register all the hooks for the admin portion of the plugin.
	 */
	public function add_hooks() {
		add_action( 'admin_menu', array( $this, 'build_menu' ) );

		global $pagenow;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_new_post_type_page  = 'post-new.php' === $pagenow && ! empty( $_GET['post_type'] ) && 'page' === $_GET['post_type'];
		$is_express_editor_page = 'admin.php' === $pagenow && ! empty( $_GET['page'] ) && 'wb4wp-editor' === $_GET['page'];
		$is_themes_page         = 'themes.php' === $pagenow;

		if ( $is_new_post_type_page ) {
			add_action( 'admin_init', array( $this, 'page_builder_files' ) );
		}

		if ( $is_express_editor_page ) {
			add_action( 'admin_init', array( $this, 'register_global_fonts' ) );
		}

		add_filter(
			'should_load_block_editor_scripts_and_styles',
			array( $this, 'load_block_editor_on_wb4wp' )
		);

		if ( $is_themes_page ) {
			add_action( 'admin_init', array( $this, 'register_switch_theme_confirmation_modal' ) );
		}

		if ( $this->should_show_unpublished_changes_modal() ) {
			add_action( 'admin_init', array( $this, 'register_unpublished_changes_modal' ) );
		}

		add_action( 'admin_init', array( $this, 'apply_window_middleware' ) );
		add_filter( 'page_row_actions', array( $this, 'filter_post_row_actions' ), 11, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head', array( $this, 'add_base_styles' ) );

		add_action( 'admin_bar_menu', array( $this, 'add_edit_with_wb4wp_button_to_admin_bar' ), 999 );

		add_filter( 'customize_changeset_save_data', array( $this, 'save_customize_changeset_fix' ) );
	}

	public function apply_window_middleware() {
		wp_register_script( 'window-middleware-handle', '', array( 'wp-components' ), WB4WP_PLUGIN_VERSION, false );
		wp_enqueue_script( 'window-middleware-handle' );

		$stylesheet = wp_get_theme()->get_stylesheet();
		add_inline_js_data_object(
			'window-middleware-handle',
			array(
				'root'                    => site_url( '/' ),
				'nonce'                   => wp_create_nonce( 'wp_rest' ),
				'previewNonce'            => wp_create_nonce( 'preview-customize_' . $stylesheet ),
				'saveNonce'               => wp_create_nonce( 'save-customize_' . $stylesheet ),
				'creativeMailInstalled'   => Creative_Mail_Helper::is_creative_mail_installed(),
				'pluginVersion'           => WB4WP_PLUGIN_VERSION,
				'themeVersion'            => Theme_Manager::get_current_theme_version(),
				'themeSlug'               => Theme_Manager::get_current_theme_slug(),
				'themeName'               => Theme_Manager::get_current_theme_name(),
				'restApiRoot'             => untrailingslashit( get_rest_url() ),
				'wooCommerceActive'       => Woo_Commerce_Manager::is_plugin_active(),
				'footerBackgroundSupport' => true,
			)
		);
	}

	/**
	 * Function that handles the admin_enqueue_scripts hook
	 */
	public function admin_enqueue_scripts() {
		$this->disable_gutenberg_popup();
		$this->register_gutenberg_scripts();
		$this->load_gutenberg();
	}

	public function load_gutenberg() {
		$current_screen = get_current_screen();

		if ( empty( $current_screen ) || 'toplevel_page_wb4wp-editor' !== $current_screen->id ) {
			return;
		}

		// phpcs:ignore
		if ( ! empty( $_GET['wb4wp-post-id'] ) ) {
			// phpcs:ignore
			$post_id = $_GET['wb4wp-post-id'];
		} else {
			$post_id = get_option( 'page_on_front' );
		}

		$post = get_post( _sanitize_text_fields( $post_id ) );

		wp_enqueue_script( 'wp-edit-post' );
		wp_enqueue_script( 'wp-format-library' );

		if ( empty( $post ) ) {
			$categories = array(
				array(
					'slug'  => 'text',
					'title' => 'Text',
					'icon'  => null,
				),
				array(
					'slug'  => 'media',
					'title' => 'Media',
					'icon'  => null,
				),
				array(
					'slug'  => 'design',
					'title' => 'Design',
					'icon'  => null,
				),
				array(
					'slug'  => 'widgets',
					'title' => 'Widgets',
					'icon'  => null,
				),
				array(
					'slug'  => 'embed',
					'title' => 'Embed',
					'icon'  => null,
				),
				array(
					'slug'  => 'reusable',
					'title' => 'Reusable Blocks',
					'icon'  => null,
				),
			);
		} else {
			$categories = get_block_categories( $post );
		}

		wp_add_inline_script(
			'wp-blocks',
			sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( $categories ) ),
			'after'
		);

		wp_add_inline_script(
			'wp-blocks',
			'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');'
		);

		wp_enqueue_editor();

		wp_enqueue_media();

		/**
		 * Styles
		 */
		wp_enqueue_style( 'wp-edit-post' );
		wp_enqueue_style( 'wp-format-library' );

		do_action( 'enqueue_block_editor_assets' );

		wp_add_inline_script( 'wp-block-library', 'wp.blockLibrary.registerCoreBlocks()', 'after' );
	}

	/**
	 * Loads the block editor js in our editor page
	 *
	 * @param $bool
	 *
	 * @return bool
	 */
	public function load_block_editor_on_wb4wp( $bool ) {
		$current_screen = get_current_screen();

		if ( ! empty( $current_screen ) && 'toplevel_page_wb4wp-editor' === $current_screen->id ) {
			return true;
		}

		return $bool;
	}

	public function register_gutenberg_scripts() {
		wp_enqueue_style( 'font-awesome', 'https://components.mywebsitebuilder.com/fonts/font-awesome.css', false, WB4WP_PLUGIN_VERSION );
	}

	/**
	 * Disables the Gutenberg welcome popup
	 */
	public function disable_gutenberg_popup() {
		wp_add_inline_script( 'wp-edit-post', 'wp.data.select("core/edit-post").isFeatureActive("welcomeGuide") && wp.data.dispatch("core/edit-post").toggleFeature("welcomeGuide")' );
	}

	public function filter_post_row_actions( $actions, $post ) {
		if ( Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ) !== 'onboarded' ) {
			return $actions;
		}

		if ( get_post_meta( $post->ID, '_wp_page_template', true ) === 'wb4wp-template.php' ) {
			$actions['edit_with_wb4wp_'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url( 'admin.php?page=wb4wp-editor&wb4wp-post-id=' . $post->ID ),
				// phpcs:ignore
				__( 'Edit with ' . WB4WP_PLUGIN_NAME, WB4WP_WP_TEXT_DOMAIN )
			);
		}

		return $actions;
	}

	public function go_to_page_editor() {
		wp_nonce_field( 'wp_rest' );

		$block_name = 'open-wb4wp-editor';

		$this->add_scripts_and_styling( $block_name );

		$ping_url = Environment_Helper::get_app_gateway_url( 'wb4wp/v1.0/instance/ping' );

		add_inline_js_data_object(
			$block_name,
			array(
				'wpAdminUrl'   => admin_url(),
				'pluginState'  => Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ),
				'provider'     => $this->provider,
				'providerKey'  => Provider_Helper::get_provider(),
				'providerHome' => Environment_Helper::get_app_url(),
				'pingUrl'      => $ping_url,
				'dashboardUrl' => Environment_Helper::get_dashboard_url(),
			)
		);
	}

	public function add_scripts_and_styling( $block_name, $assets = array( 'css', 'js' ) ) {
		if ( empty( $block_name ) ) {
			throw new Exception( 'No block Name specified!' );
		}

		$asset_file = include_once WB4WP_PLUGIN_DIR . 'build/' . $block_name . '.asset.php';

		if ( in_array( 'css', $assets, true ) ) {
			wp_enqueue_style(
				$block_name,
				WB4WP_PLUGIN_URL . 'build/' . $block_name . '.css',
				array(),
				$asset_file[ VERSION ]
			);
		}

		if ( in_array( 'js', $assets, true ) ) {
			wp_enqueue_script(
				$block_name,
				WB4WP_PLUGIN_URL . 'build/' . $block_name . '.js',
				$asset_file[ DEPENDENCIES ],
				$asset_file[ VERSION ],
				false
			);
		}
	}

	public function go_to_onboarding() {
		$landing_url  = admin_url( 'admin.php?page=wb4wp-editor' );
		$is_onboarded = Provider_Helper::is_bluehost() && Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ) !== 'new';

		if ( ! $is_onboarded && ! Provider_Helper::is_bluehost() ) {
			$landing_url = Wb4wp::get_instance()->get_wb4wp_manager()->get_signup_url();
		}

		$ping_url = Environment_Helper::get_app_gateway_url( 'wb4wp/v1.0/instance/ping' );

		$block_name = 'onboarding';

		switch ( Provider_Helper::get_provider() ) {
			case Provider_Names::BLUEHOST:
				$domain = 'Bluehost.com';
				break;
			case Provider_Names::BLUEHOST_INDIA:
				$domain = 'Bluehost.in';
				break;
			case Provider_Names::BLUEHOST_ASIA:
				$domain = 'Bluehostasia.com';
				break;
			default:
				$domain = 'Websitebuilder.com';
		}

		echo '<div id="website-builder-onboarding"></div>';

		$this->add_scripts_and_styling( $block_name );

		add_inline_js_data_object(
			$block_name,
			array(
				'launchUrl'      => $landing_url,
				'pingUrl'        => $ping_url,
				'provider'       => $this->provider,
				'providerDomain' => $domain,
			)
		);
	}

	/**
	 * Will build the menu for WP-Admin
	 */
	public function build_menu() {
		$capability = 'manage_options';

		// phpcs:ignore
		$menu_title = esc_html__( WB4WP_PLUGIN_NAME, WB4WP_WP_TEXT_DOMAIN );
		$icon       = 'icon.svg';

		if ( Provider_Helper::is_bluehost() ) {
			$menu_title = 'Website Builder';
			$icon       = 'bluehost-wp-admin.svg';
		}

		$setup_completed = Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ) === 'new' && ! Provider_Helper::is_bluehost();
		if ( $setup_completed ) {
			add_menu_page(
				WB4WP_PLUGIN_NAME,
				$menu_title,
				$capability,
				'wb4wp-editor',
				array( $this, 'go_to_onboarding' ),
				WB4WP_PLUGIN_URL . $icon,
				60
			);
		} else {
			add_menu_page(
				WB4WP_PLUGIN_NAME,
				$menu_title,
				$capability,
				'wb4wp-editor',
				array( $this, 'go_to_page_editor' ),
				WB4WP_PLUGIN_URL . $icon,
				60
			);
		}
	}

	public function register_global_fonts() {
		if ( Provider_Helper::is_bluehost() ) {
			wp_enqueue_style( 'load-style-1', 'https://use.typekit.net/tqg1vaa.css', false, WB4WP_PLUGIN_VERSION );
			wp_add_inline_style( 'load-style-1', " *{font-family: 'proxima-nova',sans-serif}" );
			// when you want to support 2 fonts add this:
			// h1 > span, h2 > span, h3 > span {font-family: 'source-serif-pro'}.
		} else {
			wp_enqueue_style( 'load-style-1', 'https://fonts.googleapis.com/css?family=Nunito+Sans:400,700', false, WB4WP_PLUGIN_VERSION );
			wp_add_inline_style( 'load-style-1', '*{font-family: Nunito Sans;}' );
		}

		wp_enqueue_style( 'load-style-2', 'https://fonts.googleapis.com/icon?family=Material+Icons', false, WB4WP_PLUGIN_VERSION );
	}

	/**
	 * Registers all assets so that they will be loaded at the admin pages
	 */
	public function page_builder_files() {
		$block_name = 'new-page-empty-state-page-type-selector';

		$this->add_scripts_and_styling( $block_name );

		add_inline_js_data_object(
			$block_name,
			array(
				'provider' => $this->provider,
			)
		);
	}

	/**
	 * Registers the confirmation modal that triggers when adding or switching themes.
	 */
	public function register_switch_theme_confirmation_modal() {
		$block_name = 'switch-theme-confirmation';

		$this->add_scripts_and_styling( $block_name );

		add_inline_js_data_object(
			$block_name,
			array(
				'disablePluginUrl' => get_rest_url( null, '/wb4wp/v1/disable-plugin' ),
			)
		);
	}

	/**
	 * @throws Exception
	 */
	public function register_unpublished_changes_modal() {
		$this->add_scripts_and_styling( 'unpublished-changes-modal' );
	}

	/**
	 * Generates a menu link based
	 * on the current plugin dir
	 *
	 * @param $route
	 *
	 * @return string
	 */
	public function generate_menu_link( $route ) {
		return WB4WP_PLUGIN_DIR_NAME . $route;
	}

	public function add_base_styles() {
		global $post;

		if ( ! empty( $post ) ) {
			Template_Manager::output_essential_scripts_and_json_objects_used_by_express_editor_runtime( $post->ID );
		}
	}

	public function add_edit_with_wb4wp_button_to_admin_bar( $wp_admin_bar ) {
		global $post;
		if ( empty( $post ) ) {
			return;
		}

		$post_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( 'wb4wp-template.php' !== $post_template ) {
			return;
		}

		$icon = 'icon.svg';
		if ( Provider_Helper::is_bluehost() ) {
			$icon = 'bluehost-wp-admin.svg';
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "
            <style>
            #wpadminbar .edit-with-wb4wp .ab-item:before {
                content: '';
                background-image: url('" . WB4WP_PLUGIN_URL . $icon . "') !important;
                width: 1em;
                height: 1em;
                background-repeat: no-repeat;
                top: 50%;
                transform: translateY(-0.5em);
            }

            #wpadminbar .edit-with-wb4wp:hover .ab-item:before, #wpadminbar .edit-with-wb4wp .ab-item:focus:before {
                filter: brightness(0) saturate(100%) invert(59%) sepia(47%) saturate(2341%) hue-rotate(151deg) brightness(93%) contrast(102%);
            }
            </style>
        ";
		//phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

		$wp_admin_bar->add_node(
			array(
				'id'    => 'edit_with_wb4wp',
				// phpcs:ignore
				'title' => sprintf( __( 'Edit with %s', WB4WP_WP_TEXT_DOMAIN ), Provider_Helper::get_provider_name() ),
				'href'  => admin_url( 'admin.php?page=wb4wp-editor&wb4wp-post-id=' . $post->ID ),
				'meta'  => array(
					'class' => 'edit-with-wb4wp',
				),
			)
		);
	}

	public function save_customize_changeset_fix( $data ) {
		foreach ( $data as &$setting_data ) {
			if ( isset( $setting_data['value'] ) ) {
				$value = $setting_data['value'];

				if ( false === $value ) {
					$value = 'false';
				} elseif ( true === $value ) {
					$value = 'true';
				}

				$setting_data['value'] = $value;
			}
		}

		return $data;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	/**
	 * @return bool
	 */
	private function should_show_unpublished_changes_modal() {
		global  $pagenow;
		$is_edit_page = 'post.php' === $pagenow && ! empty( $_GET['action'] ) && 'edit' === $_GET['action'];
		$post_id      = ! empty( $_GET['post'] ) ? $_GET['post'] : null;

		return (
			$is_edit_page // Only on the edit page.
			&& get_option( 'wb4wp-show-unpublished-changes-modal' ) !== 'false' // Does the user want to see the modal?
			&& Post_Helper::has_unpublished_changes( $post_id ) // Do we have any unpublished changes?
			&& empty( $_GET['revision'] ) // Are we refreshing after restoring a revision?
		);
	}
	// phpcs:enable  WordPress.Security.NonceVerification.Recommended

}
