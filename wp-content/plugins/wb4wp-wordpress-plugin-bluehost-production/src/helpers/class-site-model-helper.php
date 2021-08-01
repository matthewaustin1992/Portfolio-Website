<?php
namespace Wb4Wp\Helpers;

use Wb4Wp\Integrations\Exceptions\Theme_Not_Active_Exception;
use Wb4Wp\Integrations\Theme_Integration;

/**
 * Class SiteModelHelper
 * @package Wb4Wp\Helpers
 */
final class Site_Model_Helper {

	/**
	 * @param array $site_model .
	 *
	 * @throws Theme_Not_Active_Exception
	 */
	public static function apply_theme_options_to_site_model( &$site_model ) {
		$global_binding = &$site_model['globalBinding'];

		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$main_page = null;
		foreach ( $site_model['pages'] as &$page ) {
			if ( ! $page['mainPage'] ) {
				continue;
			}

			$main_page = &$page;
			break;
		}

		$header_section = &$main_page['headerSection'];
		$footer_section = &$site_model['globalSections']['footer'];
		$theme_colors   = &$site_model['theme']['colors'];
		$theme_fonts    = &$site_model['theme']['fonts'];

		$mappings = array(
			'wb4wp_header_section_layout_setting'          => array( &$header_section['layout']['section'], 'id' ),
			'wb4wp_header_section_social_buttons_setting'  => array( &$header_section['binding']['_toggle'], 'global.accounts' ),
			'wb4wp_header_section_site_title_setting'      => array( &$header_section['binding']['_toggle'], 'global.title' ),
			'wb4wp_header_section_fixed_navigation_bar_setting' => array( &$header_section['binding'], 'fixedNavigation' ),

			'wb4wp_footer_section_layout_setting'          => array( &$footer_section['layout']['section'], 'id' ),
			'wb4wp_footer_section_social_buttons_setting'  => array( &$footer_section['binding']['_toggle'], 'global.accounts' ),
			'wb4wp_footer_section_site_title_setting'      => array( &$footer_section['binding']['_toggle'], 'global.title' ),
			'wb4wp_footer_section_description_setting'     => array( &$footer_section['binding'], 'description' ),
			'wb4wp_footer_section_description_toggle_setting' => array( &$footer_section['binding']['_toggle'], 'description' ),
			'wb4wp_footer_section_address_toggle_setting'  => array( &$footer_section['binding'], 'addressToggle' ),
			'wb4wp_footer_section_email_setting'           => array( &$footer_section['binding']['_toggle'], 'global.email' ),
			'wb4wp_footer_section_phone_setting'           => array( &$footer_section['binding']['_toggle'], 'global.phone' ),
			'wb4wp_footer_section_copyright_message_setting' => array( &$footer_section['binding']['_toggle'], 'copyrightHelper' ),
			'wb4wp_footer_section_link_to_sitemap_setting' => array( &$footer_section['binding']['_toggle'], 'sitemapToggle' ),
			'wb4wp_footer_section_page_menu_setting'       => array( &$footer_section['binding'], 'pagesMenu' ),
			'wb4wp_footer_section_background_color_index_setting' => array( &$footer_section['binding']['background'], 'colorIndex' ),
			'wb4wp_footer_section_background_image_setting' => array( &$footer_section['binding']['background']['img']['sizes'], '1080' ),
			'wb4wp_footer_section_background_video_setting' => array( &$footer_section['binding']['background'], 'video' ),
			'wb4wp_footer_section_background_opacity_setting' => array( &$footer_section['binding']['background'], 'opacity' ),
			'wb4wp_footer_section_background_effect_setting' => array( &$footer_section['binding']['background'], 'effect' ),
			'wb4wp_footer_section_background_pattern_index_setting' => array( &$footer_section['binding']['background'], 'patternIndex' ),

			'wb4wp_social_accounts_section_facebook_setting' => array( &$global_binding['accounts'], 'facebook' ),
			'wb4wp_social_accounts_section_twitter_setting' => array( &$global_binding['accounts'], 'twitter' ),
			'wb4wp_social_accounts_section_instagram_setting' => array( &$global_binding['accounts'], 'instagram' ),
			'wb4wp_social_accounts_section_linkedin_setting' => array( &$global_binding['accounts'], 'linkedin' ),
			'wb4wp_social_accounts_section_pinterest_setting' => array( &$global_binding['accounts'], 'pinterest' ),
			'wb4wp_social_accounts_section_youtube_setting' => array( &$global_binding['accounts'], 'youtube' ),
			'wb4wp_social_accounts_section_opentable_setting' => array( &$global_binding['accounts'], 'opentable' ),

			'wb4wp_contact_information_section_phone_number_setting' => array( &$global_binding, 'phone' ),
			'wb4wp_contact_information_section_email_setting' => array( &$global_binding, 'email' ),
			'wb4wp_contact_information_section_street_setting' => array( &$global_binding['address'], 'street' ),
			'wb4wp_contact_information_section_city_setting' => array( &$global_binding['address'], 'city' ),
			'wb4wp_contact_information_section_zip_code_setting' => array( &$global_binding['address'], 'zip' ),
			'wb4wp_contact_information_section_state_setting' => array( &$global_binding['address'], 'state' ),
			'wb4wp_contact_information_section_country_setting' => array( &$global_binding['address'], 'country' ),

			'wb4wp_color_section_accent1_setting'          => array( &$theme_colors['accent'], 0 ),
			'wb4wp_color_section_accent2_setting'          => array( &$theme_colors['accent'], 1 ),
			'wb4wp_color_section_background_setting'       => array( &$theme_colors, 'background' ),
			'wb4wp_color_section_text_setting'             => array( &$theme_colors, 'text' ),

			'wb4wp_fonts_section_body_setting'             => array( &$theme_fonts, 'body' ),
			'wb4wp_fonts_section_heading_setting'          => array( &$theme_fonts, 'heading' ),
			'wb4wp_fonts_section_font_size_setting'        => array( &$theme_fonts, 'fontSize' ),

			'wb4wp_logo_section_url_setting'               => array( &$global_binding['logo'], 'value' ),
			'wb4wp_logo_section_size_setting'              => array( &$global_binding['logo'], 'logoSizeClass' ),
			'wb4wp_logo_section_show_in_header_setting'    => array( &$header_section['binding']['_toggle'], 'global.logo' ),
			'wb4wp_logo_section_show_in_footer_setting'    => array( &$footer_section['binding']['_toggle'], 'global.logo' ),
		);

		foreach ( $mappings as $theme_setting_key => $property_path ) {
			$setting = Theme_Integration::get_customize_setting( $theme_setting_key );

			if ( ! isset( $setting ) ) {
				continue;
			}

			if ( strpos( $theme_setting_key, 'social_accounts_section' ) !== false && empty( $setting ) ) {
				continue;
			}

			$parent = &$property_path[0];
			$key    = $property_path[1];

			switch ( $theme_setting_key ) {
				case 'wb4wp_footer_section_layout_setting':
					$setting = self::map_wp_footer_layout_id_to_ee( $setting );
					break;

				case 'wb4wp_color_section_accent1_setting':
				case 'wb4wp_color_section_accent2_setting':
				case 'wb4wp_color_section_background_setting':
				case 'wb4wp_color_section_text_setting':
					$setting = Color_Helper::hex_to_rgb( $setting );
					break;

				case 'wb4wp_fonts_section_body_setting':
				case 'wb4wp_fonts_section_heading_setting':
					$setting = self::map_wp_font_to_ee( $setting );
					break;
				case 'wb4wp_logo_section_size_setting':
					$setting = 'kv-ee-logo-' . $setting;
					break;

				case 'wb4wp_fonts_section_font_size_setting':
					$setting = (float) $setting;
					break;
				case 'wb4wp_footer_section_background_color_index_setting':
					$setting = (float) $setting;
					break;
			}

			if ( empty( $parent ) || ! is_array( $parent ) ) {
				$parent = array( $key => $setting );
			} else {
				$parent[ $key ] = $setting;
			}
		}

		$site_model['theme']['fonts']['customFont'] = true;
	}

	private static function map_wp_footer_layout_id_to_ee( $wp_footer_layout_id ) {
		switch ( $wp_footer_layout_id ) {
			case 'footer-2':
				return 'mudoce19';
			case 'footer-3':
				return 'bajigu70';
			case 'footer-4':
				return 'bajigu69';
			case 'footer-5':
				return 'bajigu99';
			case 'footer-1':
			default:
				return 'bajigu80';
		}
	}

	private static function map_wp_font_to_ee( $wp_font ) {
		$split = explode( ':', $wp_font );
		return array(
			'name'   => $split[0],
			'weight' => $split[1],
		);
	}

	public static function map_site_model_to_theme_options( $site_model ) {
		$footer_section = $site_model['globalSections']['footer'];
		$global_binding = $site_model['globalBinding'];
		$main_pages     = array_filter(
			$site_model['pages'],
			function ( $page ) {
				return ! empty( $page['mainPage'] ) && $page['mainPage'];
			}
		);
		$main_page      = current( $main_pages );
		$header_section = $main_page['headerSection'];
		$theme_colors   = $site_model['theme']['colors'];
		$theme_fonts    = $site_model['theme']['fonts'];

		$theme_options = array();

		$theme_options['wb4wp_header_section_layout_setting']                   = $header_section['layout']['section']['id'];
		$theme_options['wb4wp_header_section_social_buttons_setting']           = ! empty( $header_section['binding']['_toggle']['global.accounts'] ) ? 'true' : 'false';
		$theme_options['wb4wp_header_section_site_title_setting']               = ! empty( $header_section['binding']['_toggle']['global.title'] ) ? 'true' : 'false';
		$theme_options['wb4wp_header_section_fixed_navigation_bar_setting']     = ! empty( $header_section['binding']['fixedNavigation'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_layout_setting']                   = self::map_ee_footer_layout_id_to_wp( $footer_section['layout']['section']['id'] );
		$theme_options['wb4wp_footer_section_social_buttons_setting']           = ! empty( $footer_section['binding']['_toggle']['global.accounts'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_site_title_setting']               = ! empty( $footer_section['binding']['_toggle']['global.title'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_description_setting']              = isset( $footer_section['binding']['description'] ) ? $footer_section['binding']['description'] : '';
		$theme_options['wb4wp_footer_section_description_toggle_setting']       = ! empty( $footer_section['binding']['_toggle']['description'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_address_toggle_setting']           = ! empty( $footer_section['binding']['addressToggle'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_email_setting']                    = ! empty( $footer_section['binding']['_toggle']['global.email'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_phone_setting']                    = ! empty( $footer_section['binding']['_toggle']['global.phone'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_copyright_message_setting']        = ! empty( $footer_section['binding']['_toggle']['copyrightHelper'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_link_to_sitemap_setting']          = ! empty( $footer_section['binding']['_toggle']['sitemapToggle'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_page_menu_setting']                = ! empty( $footer_section['binding']['pagesMenu'] ) ? 'true' : 'false';
		$theme_options['wb4wp_footer_section_background_color_index_setting']   = ! empty( $footer_section['binding']['background']['colorIndex'] ) ? $footer_section['binding']['background']['colorIndex'] : '0';
		$theme_options['wb4wp_footer_section_background_image_setting']         = ! empty( $footer_section['binding']['background']['img']['value'] ) ? $footer_section['binding']['background']['img']['value'] : ( ! empty( $footer_section['binding']['background']['img']['sizes']['1080'] ) ? $footer_section['binding']['background']['img']['sizes']['1080'] : '' );
		$theme_options['wb4wp_footer_section_background_video_setting']         = ! empty( $footer_section['binding']['background']['video'] ) ? $footer_section['binding']['background']['video'] : '';
		$theme_options['wb4wp_footer_section_background_opacity_setting']       = ! empty( $footer_section['binding']['background']['opacity'] ) ? $footer_section['binding']['background']['opacity'] : '';
		$theme_options['wb4wp_footer_section_background_effect_setting']        = ! empty( $footer_section['binding']['background']['effect'] ) ? $footer_section['binding']['background']['effect'] : '';
		$theme_options['wb4wp_footer_section_background_pattern_index_setting'] = ! empty( $footer_section['binding']['background']['patternIndex'] ) ? $footer_section['binding']['background']['patternIndex'] : '';
		$theme_options['wb4wp_logo_section_url_setting']                        = ! empty( $global_binding['logo']['value'] ) ? $global_binding['logo']['value'] : '';
		$theme_options['wb4wp_logo_section_size_setting']                       = ! empty( $global_binding['logo']['logoSizeClass'] ) ? str_replace( 'kv-ee-logo-', '', $global_binding['logo']['logoSizeClass'] ) : 'medium';
		$theme_options['wb4wp_logo_section_show_in_header_setting']             = ! empty( $header_section['binding']['_toggle']['global.logo'] ) ? 'true' : 'false';
		$theme_options['wb4wp_logo_section_show_in_footer_setting']             = ! empty( $footer_section['binding']['_toggle']['global.logo'] ) ? 'true' : 'false';

		$social_accounts = array(
			'facebook',
			'twitter',
			'instagram',
			'linkedin',
			'pinterest',
			'youtube',
			'opentable',
		);
		foreach ( $social_accounts as $social_account ) {
			if ( empty( $global_binding['accounts'][ $social_account ] ) ) {
				continue;
			}

			$theme_options[ 'wb4wp_social_accounts_section_' . $social_account . '_setting' ] = $global_binding['accounts'][ $social_account ];
		}

		$theme_options['wb4wp_contact_information_section_phone_number_setting'] = isset( $global_binding['phone'] ) ? $global_binding['phone'] : '';
		$theme_options['wb4wp_contact_information_section_email_setting']        = isset( $global_binding['email'] ) ? $global_binding['email'] : '';
		$theme_options['wb4wp_contact_information_section_street_setting']       = isset( $global_binding['address']['street'] ) ? $global_binding['address']['street'] : '';
		$theme_options['wb4wp_contact_information_section_city_setting']         = isset( $global_binding['address']['city'] ) ? $global_binding['address']['city'] : '';
		$theme_options['wb4wp_contact_information_section_zip_code_setting']     = isset( $global_binding['address']['zip'] ) ? $global_binding['address']['zip'] : '';
		$theme_options['wb4wp_contact_information_section_state_setting']        = isset( $global_binding['address']['state'] ) ? $global_binding['address']['state'] : '';
		$theme_options['wb4wp_contact_information_section_country_setting']      = isset( $global_binding['address']['country'] ) ? $global_binding['address']['country'] : '';

		if ( isset( $theme_colors['accent'][0] ) ) {
			$theme_options['wb4wp_color_section_accent1_setting'] = Color_Helper::rgb_to_hex( $theme_colors['accent'][0] );
		}

		if ( isset( $theme_colors['accent'][1] ) ) {
			$theme_options['wb4wp_color_section_accent2_setting'] = Color_Helper::rgb_to_hex( $theme_colors['accent'][1] );
		}

		if ( isset( $theme_colors['background'] ) ) {
			$theme_options['wb4wp_color_section_background_setting'] = Color_Helper::rgb_to_hex( $theme_colors['background'] );
		}

		if ( isset( $theme_colors['text'] ) ) {
			$theme_options['wb4wp_color_section_text_setting'] = Color_Helper::rgb_to_hex( $theme_colors['text'] );
		}

		if ( isset( $theme_fonts['body'] ) ) {
			$theme_options['wb4wp_fonts_section_body_setting'] = self::map_ee_font_to_wp( $theme_fonts['body'] );
		}

		if ( isset( $theme_fonts['heading'] ) ) {
			$theme_options['wb4wp_fonts_section_heading_setting'] = self::map_ee_font_to_wp( $theme_fonts['heading'] );
		}

		if ( isset( $theme_fonts['fontSize'] ) ) {
			$theme_options['wb4wp_fonts_section_font_size_setting'] = $theme_fonts['fontSize'];
		}

		return $theme_options;
	}

	private static function map_ee_footer_layout_id_to_wp( $ee_footer_layout_id ) {
		switch ( $ee_footer_layout_id ) {
			case 'mudoce19':
				return 'footer-2';
			case 'bajigu77':
			case 'bajigu70':
				return 'footer-3';
			case 'bajigu78':
			case 'bajigu69':
				return 'footer-4';
			case 'bajigu79':
			case 'bajigu99':
				return 'footer-5';
			case 'bajigu80':
			default:
				return 'footer-1';
		}
	}

	private static function map_ee_font_to_wp( $ee_font ) {
		return $ee_font['name'] . ':' . $ee_font['weight'];
	}

}
