<?php

namespace Wb4WpTheme\Managers\Customize;

use Wb4WpTheme\Helpers\Font_Helper;
use Wb4WpTheme\Helpers\Object_Helper;

/**
 * Interface for retrieving and settings in the database.
 */
class Customize_Settings {
	/**
	 * Settings object
	 *
	 * @var $settings
	 */
	private static $settings;

	/**
	 * Get defined theme settings
	 */
	private static function get_settings() {
		if ( empty( self::$settings ) ) {
			self::$settings = array(
				'header'              => array(
					'layout'               => array(
						'label'       => __( 'Layout', 'wb4wp_theme' ),
						'description' => __( 'The layout to use', 'wb4wp_theme' ),
						'type'        => 'select',
						'default'     => 'navigation-1',
						'choices'     => array(
							'navigation-1' => 'Navigation 1',
							'navigation-2' => 'Navigation 2',
							'navigation-3' => 'Navigation 3',
							'navigation-4' => 'Navigation 4',
							'navigation-5' => 'Navigation 5',
							// 'navigation-6' => 'Navigation 6',  disable this menu item
						),
					),
					'social_buttons'       => self::get_social_buttons_setting( 'true' ),
					'site_title'           => self::get_site_title_setting( 'true' ),
					'fixed_navigation_bar' => array(
						'label'       => __( 'Fixed navigation bar', 'wb4wp_theme' ),
						'description' => __( 'Keep navigation bar fixed to the top of the screen when scrolling', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
				),
				'footer'              => array(
					'layout'             => array(
						'label'       => __( 'Layout', 'wb4wp_theme' ),
						'description' => __( 'The layout to use', 'wb4wp_theme' ),
						'type'        => 'select',
						'default'     => 'footer-1',
						'choices'     => array(
							'footer-1' => 'Footer 1',
							'footer-2' => 'Footer 2',
							'footer-3' => 'Footer 3',
							'footer-4' => 'Footer 4',
							'footer-5' => 'Footer 5',
						),
					),
					'social_buttons'     => self::get_social_buttons_setting( 'true' ),
					'site_title'         => self::get_site_title_setting( 'true' ),
					'description'        => array(
						'label'       => __( 'Site description', 'wb4wp_theme' ),
						'description' => __( 'add a site description here', 'wb4wp_theme' ),
						'type'        => 'text',
						'default'     => 'Add a description here',
					),
					'description_toggle' => array(
						'label'       => __( 'Description', 'wb4wp_theme' ),
						'description' => __( 'Show your site description', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'address_toggle'     => array(
						'label'       => __( 'Address', 'wb4wp_theme' ),
						'description' => __( 'Show your address of your company', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'email'              => array(
						'label'       => __( 'Email', 'wb4wp_theme' ),
						'description' => __( 'Show your email', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'phone'              => array(
						'label'       => __( 'Phone', 'wb4wp_theme' ),
						'description' => __( 'Show your phonenumber', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'copyright_message'  => array(
						'label'       => __( 'Copyright message', 'wb4wp_theme' ),
						'description' => __( 'Display the copyright message', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'false',
					),
					'link_to_sitemap'    => array(
						'label'       => __( 'Sitemap', 'wb4wp_theme' ),
						'description' => __( 'Display the link to the sitemap', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'page_menu'          => array(
						'label'       => __( 'Page menu', 'wb4wp_theme' ),
						'description' => __( 'Display the page menu', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
				),
				'social_accounts'     => array(
					'facebook'  => array(
						'label'   => __( 'Facebook', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
					'twitter'   => array(
						'label'   => __( 'Twitter', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
					'instagram' => array(
						'label'   => __( 'Instagram', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
					'linkedin'  => array(
						'label'   => __( 'LinkedIn', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
					'pinterest' => array(
						'label'   => __( 'Pinterest', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
					'youtube'   => array(
						'label'   => __( 'YouTube', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
					'opentable' => array(
						'label'   => __( 'OpenTable', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '',
					),
				),
				'contact_information' => array(
					'phone_number' => array(
						'label'   => __( 'Phone number', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '0123456789',
					),
					'email'        => array(
						'label'   => __( 'Email address', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => 'info@example.com',
					),
					'street'       => array(
						'label'   => __( 'Address', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '10 Corporate Drive',
					),
					'city'         => array(
						'label'   => __( 'City', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => 'Burlington',
					),
					'zip_code'     => array(
						'label'   => __( 'Zipcode', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => '01803',
					),
					'state'        => array(
						'label'   => __( 'State', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => 'MA',
					),
					'country'      => array(
						'label'   => __( 'Country', 'wb4wp_theme' ),
						'type'    => 'text',
						'default' => 'United States',
					),
				),
				'color'               => array(
					'accent1'    => array(
						'label'       => __( 'Accent 1', 'wb4wp_theme' ),
						'description' => __( 'The primary color', 'wb4wp_theme' ),
						'type'        => 'color',
						'default'     => '#4f8a8b',
					),
					'accent2'    => array(
						'label'       => __( 'Accent 2', 'wb4wp_theme' ),
						'description' => __( 'An accent color', 'wb4wp_theme' ),
						'type'        => 'color',
						'default'     => '#fbd46d',
					),
					'background' => array(
						'label'       => __( 'Background', 'wb4wp_theme' ),
						'description' => __( 'A background color', 'wb4wp_theme' ),
						'type'        => 'color',
						'default'     => '#eeeeee',
					),
					'text'       => array(
						'label'       => __( 'Text', 'wb4wp_theme' ),
						'description' => __( 'The text color', 'wb4wp_theme' ),
						'type'        => 'color',
						'default'     => '#222831',
					),
				),
				'fonts'               => array(
					'heading'   => array_merge_recursive(
						self::parse_font_settings( Font_Helper::instance()->get_heading_fonts() ),
						array(
							'label'       => __( 'Title font', 'wb4wp_theme' ),
							'description' => __( 'The title font used in the header and footer', 'wb4wp_theme' ),
						)
					),
					'body'      => array_merge_recursive(
						self::parse_font_settings( Font_Helper::instance()->get_body_fonts() ),
						array(
							'label'       => __( 'Paragraph font', 'wb4wp_theme' ),
							'description' => __( 'The paragraph google font used in the header and footer', 'wb4wp_theme' ),
						)
					),
					'font_size' => array(
						'label'       => __( 'Font size', 'wb4wp_theme' ),
						'description' => __( 'Set the font size for your website', 'wb4wp_theme' ),
						'type'        => 'select',
						'default'     => '1',
						'choices'     => array(
							'0.875' => 'Small',
							'1'     => 'Medium',
							'1.125' => 'Large',
						),
					),
				),
				'logo'                => array(
					'url'            => array(
						'label'       => __( 'Logo URL', 'wb4wp_theme' ),
						'description' => __( 'Enter the URL to your logo here', 'wb4wp_theme' ),
						'type'        => 'text',
						'default'     => '',
					),
					'size'           => array(
						'label'       => __( 'Logo size', 'wb4wp_theme' ),
						'description' => __( 'Adjust the logo size', 'wb4wp_theme' ),
						'type'        => 'select',
						'default'     => 'medium',
						'choices'     => array(
							'small'       => 'Small',
							'medium'      => 'Medium',
							'large'       => 'Large',
							'extra-large' => 'Extra Large',
						),
					),
					'show_in_header' => array(
						'label'       => __( 'Show in the header', 'wb4wp_theme' ),
						'description' => __( 'Has no effect when the logo URL has not been configured', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'false',
					),
					'show_in_footer' => array(
						'label'       => __( 'Show in the footer', 'wb4wp_theme' ),
						'description' => __( 'Has no effect when the logo URL has not been configured', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'false',
					),
				),
				'single_post'         => array(
					'layout'                => array(
						'label'   => __( 'Layout style', 'wb4wp_theme' ),
						'type'    => 'select',
						'default' => 'single-post-1',
						'choices' => array(
							'single-post-1' => 'Single Post 1',
							'single-post-2' => 'Single Post 2',
						),
					),
					'show_cover_image'      => array(
						'label'       => __( 'Cover image', 'wb4wp_theme' ),
						'description' => __( 'Use featured images as cover.', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'place_title_in_cover'  => array(
						'label'       => __( 'Place title in cover', 'wb4wp_theme' ),
						'description' => __( 'Add blog post to the cover.', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'show_author'           => array(
						'label'   => __( 'Author', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_author_image'     => array(
						'label'   => __( 'Author image', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'false',
					),
					'show_publication_date' => array(
						'label'   => __( 'Publication date', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_social_sharing'   => array(
						'label'   => __( 'Social sharing', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_tags'             => array(
						'label'   => __( 'Tags', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'false',
					),
				),
				'blog_overview'       => array(
					'layout'                => array(
						'label'   => __( 'Layout', 'wb4wp_theme' ),
						'type'    => 'select',
						'default' => 'blog-overview-1',
						'choices' => array(
							'blog-overview-1' => 'Blog Overview 1',
							'blog-overview-2' => 'Blog Overview 2',
							'blog-overview-3' => 'Blog Overview 3',
						),
					),
					'show_title'            => array(
						'label'   => __( 'Title', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_featured_image'   => array(
						'label'   => __( 'Featured image', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_author'           => array(
						'label'   => __( 'Author', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'false',
					),
					'show_author_image'     => array(
						'label'   => __( 'Author image', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'false',
					),
					'show_publication_date' => array(
						'label'   => __( 'Publication date', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'false',
					),
				),
				'wc_shop'             => array(
					'show_sorting_filter_toggle' => array(
						'label'       => __( 'Sorting filter', 'wb4wp_theme' ),
						'description' => __( 'Show sorting filter for filtering products', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'show_featured_images'       => array(
						'label'       => __( 'Show images', 'wb4wp_theme' ),
						'description' => __( 'Show the featured image', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
				),
				'wc_single_product'   => array(
					'show_breadcrumbs_toggle'      => array(
						'label'   => __( 'Breadcrumbs', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_meta_data_toggle'        => array(
						'label'       => __( 'Meta', 'wb4wp_theme' ),
						'description' => __( 'Show meta data, like tags and categories', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'show_description_tab_toggle'  => array(
						'label'   => __( 'Description tab', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_additional_information_tab_toggle' => array(
						'label'   => __( 'Additional info tab', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_reviews_tab_toggle'      => array(
						'label'   => __( 'Reviews tab', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
					'show_images'                  => array(
						'label'       => __( 'Image', 'wb4wp_theme' ),
						'description' => __( 'Show product images next to the description.', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'show_related_products_toggle' => array(
						'label'   => __( 'Related Products', 'wb4wp_theme' ),
						'type'    => 'toggle',
						'default' => 'true',
					),
				),
				'wc_shopping_cart'    => array(
					'show_product_image_toggle' => array(
						'label'       => __( 'Product images', 'wb4wp_theme' ),
						'description' => __( 'Show product images in the cart table', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
					'show_coupon_field_toggle'  => array(
						'label'       => __( 'Coupon field', 'wb4wp_theme' ),
						'description' => __( 'Show coupon field left under in the cart', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
				),
				'wc_checkout'         => array(
					'layout'                  => array(
						'label'   => __( 'Layout', 'wb4wp_theme' ),
						'type'    => 'select',
						'default' => 'checkout-1',
						'choices' => array(
							'checkout-1' => 'Checkout 1',
							'checkout-2' => 'Checkout 2',
						),
					),
					'show_order_notes_toggle' => array(
						'label'       => __( 'Order notes field', 'wb4wp_theme' ),
						'description' => __( 'Show order notes field in the checkout', 'wb4wp_theme' ),
						'type'        => 'toggle',
						'default'     => 'true',
					),
				),
			);
		}

		return self::$settings;
	}
	/**
	 * Parse font setttings
	 *
	 * @param array $fonts Array of fonts.
	 * @return array
	 */
	private static function parse_font_settings( $fonts ) {
		if ( empty( $fonts ) ) {
			return array();
		}

		$font_settings = array();

		foreach ( $fonts as $font ) {
			$font_settings[ "{$font['name']}:{$font['weight']}" ] = $font['name'];
		}

		$default = null;
		foreach ( $font_settings as $font_key => $font_name ) {
			$default = $font_key;
			break;
		}

		return array(
			'type'    => 'select',
			'default' => $default,
			'choices' => $font_settings,
		);
	}

	/**
	 * Get social button setting
	 *
	 * @param bool $default is default or not.
	 * @return array setting
	 */
	private static function get_social_buttons_setting( $default ) {
		$social_accounts_url = get_site_url(
			null,
			'/wp-admin/customize.php?autofocus[section]=wb4wp_social_accounts_section'
		);
		return array(
			'label'       => __( 'Social buttons', 'wb4wp_theme' ),
			'description' => sprintf(
				// translators: Includes href to Social Accounts.
				__( 'Set up your links in <a href="%s">Social Accounts</a>', 'wb4wp_theme' ),
				$social_accounts_url
			),
			'type'        => 'toggle',
			'default'     => $default,
		);
	}

	/**
	 * Get site title setting
	 *
	 * @param bool $default is default setting.
	 * @return array setting
	 */
	private static function get_site_title_setting( $default ) {
		return array(
			'label'       => __( 'Site title', 'wb4wp_theme' ),
			'description' => __( 'Display the site title', 'wb4wp_theme' ),
			'type'        => 'toggle',
			'default'     => $default,
		);
	}

	/**
	 * Retrieve a list of all the settings for the given page type. If no page type, or an unknown page type is given,
	 * a list of all settings is returned instead.
	 *
	 * @param string|null $page_type The page type.
	 *
	 * @return array
	 */
	public static function get_setting_list( $page_type = null ) {
		switch ( $page_type ) {
			case 'wp_blog_detail':
				return self::get_blog_detail_setting_list();

			case 'wp_blog_overview':
				return self::get_blog_overview_setting_list();

			case 'wp_woocommerce_shop':
				return self::get_woocommerce_shop_setting_list();

			case 'wp_woocommerce_single_product':
				return self::get_woocommerce_store_single_product_setting_list();

			case 'wp_woocommerce_shopping_cart':
				return self::get_woocommerce_shopping_cart_setting_list();

			case 'wp_woocommerce_checkout':
				return self::get_woocommerce_checkout_setting_list();

			default:
				return self::get_settings();
		}
	}

	/**
	 * Get WordPress blog detail page setting list
	 *
	 * @return array of settings.
	 */
	private static function get_blog_detail_setting_list() {
		return Object_Helper::get_recursive(
			self::get_settings(),
			array(
				'header' => array( 'layout' ),
				'single_post',
			)
		);
	}

	/**
	 * Get WordPress blog overview page setting list
	 *
	 * @return array of settings.
	 */
	private static function get_blog_overview_setting_list() {
		return Object_Helper::get_recursive(
			self::get_settings(),
			array(
				'header' => array( 'layout' ),
				'blog_overview',
			)
		);
	}

	/**
	 * Get Woocommerce checkout page setting list
	 *
	 * @return array of settings.
	 */
	private static function get_woocommerce_checkout_setting_list() {
		return Object_Helper::get_recursive(
			self::get_settings(),
			array(
				'header' => array( 'layout' ),
				'wc_checkout',
			)
		);
	}

	/**
	 * Get Woocommerce shoppingcart page setting list
	 *
	 * @return array of settings.
	 */
	private static function get_woocommerce_shopping_cart_setting_list() {
		return Object_Helper::get_recursive(
			self::get_settings(),
			array(
				'header' => array( 'layout' ),
				'wc_shopping_cart',
			)
		);
	}

	/**
	 * Get Woocommerce single product page setting list
	 *
	 * @return array of settings.
	 */
	private static function get_woocommerce_store_single_product_setting_list() {
		return Object_Helper::get_recursive(
			self::get_settings(),
			array(
				'header' => array( 'layout' ),
				'wc_single_product',
			)
		);
	}

	/**
	 * Get Woocommerce shop page setting list
	 *
	 * @return array of settings.
	 */
	private static function get_woocommerce_shop_setting_list() {
		return Object_Helper::get_recursive(
			self::get_settings(),
			array(
				'header' => array( 'layout' ),
				'wc_shop',
			)
		);
	}

	/**
	 * Retrieves a single setting's value, if set, returns default value otherwise, or null if no default value has been
	 * defined.
	 *
	 * @param string $full_setting_name Full name of the setting.
	 *
	 * @return mixed | null
	 */
	public static function get_setting( $full_setting_name ) {
		$setting_default = self::get_setting_default( $full_setting_name );
		$setting         = get_option( $full_setting_name, $setting_default );

		switch ( $setting ) {
			case 'true':
				return true;
			case 'false':
				return false;
			default:
				return $setting;
		}
	}

	/**
	 * Get default settings
	 *
	 * @param string $setting_name Name of settings.
	 * @return array settings.
	 */
	private static function get_setting_default( $setting_name ) {
		preg_match( '/wb4wp_(.*)_section_(.*)_setting/', $setting_name, $matches ); // NOSONAR .

		if ( empty( $matches ) || count( $matches ) < 3 ) {
			return null;
		}

		$section = $matches[1];
		$setting = $matches[2];

		return Object_Helper::get( self::get_settings(), array( $section, $setting, 'default' ) );
	}
}
