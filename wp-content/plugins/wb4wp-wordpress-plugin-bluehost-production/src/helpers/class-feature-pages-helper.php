<?php

namespace Wb4Wp\Helpers;

use Wb4Wp\Managers\Woo_Commerce_Manager;

/**
 * Class Feature_Pages_Helper
 * @package Wb4Wp\Helpers
 */
final class Feature_Pages_Helper {

	const FEATURE_WP_BLOG        = 'wp_blog';
	const FEATURE_WP_WOOCOMMERCE = 'wp_woocommerce';

	const BLOG_DETAIL   = 'wp_blog_detail';
	const BLOG_OVERVIEW = 'wp_blog_overview';

	const SHOP             = 'wp_woocommerce_shop';
	const SINGLE_PRODUCT   = 'wp_woocommerce_single_product';
	const SHOPPING_CART    = 'wp_woocommerce_shopping_cart';
	const CHECKOUT         = 'wp_woocommerce_checkout';
	const ACCOUNT_SETTINGS = 'wp_woocommerce_account_settings';

	const FEATURE_PAGES = array(
		self::FEATURE_WP_BLOG        => array(
			'label'      => 'WordPress Blog',
			'feature'    => self::FEATURE_WP_BLOG,
			'limitation' => self::FEATURE_WP_BLOG,
			'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><path fill="#21759B" d="M0 4a4 4 0 014-4h16a4 4 0 014 4v16a4 4 0 01-4 4H4a4 4 0 01-4-4V4z"/><path fill="#fff" d="M12 3.75c-4.548 0-8.25 3.702-8.25 8.25s3.702 8.25 8.25 8.25 8.25-3.702 8.25-8.25S16.548 3.75 12 3.75zM4.58 12c0-1.074.226-2.095.645-3.02l3.536 9.69A7.413 7.413 0 014.58 12zM12 19.42a7.24 7.24 0 01-2.095-.305l2.226-6.469 2.278 6.242.053.105A7.19 7.19 0 0112 19.42zm1.021-10.895c.446-.026.847-.07.847-.07.402-.043.35-.637-.043-.61 0 0-1.205.095-1.973.095-.725 0-1.956-.096-1.956-.096-.402-.026-.445.585-.044.612 0 0 .376.043.777.07l1.153 3.16-1.624 4.871L7.46 8.525c.446-.026.847-.07.847-.07.402-.043.35-.637-.044-.61 0 0-1.204.095-1.973.095-.14 0-.305 0-.48-.008a7.403 7.403 0 016.199-3.344c1.93 0 3.693.742 5.01 1.947-.034 0-.06-.009-.095-.009-.725 0-1.249.637-1.249 1.318 0 .612.35 1.127.725 1.738.28.497.611 1.126.611 2.043 0 .637-.244 1.37-.567 2.4l-.742 2.471-2.68-7.97zm5.492-.087a7.417 7.417 0 01-2.776 9.97l2.27-6.548c.418-1.056.567-1.903.567-2.654a16.29 16.29 0 00-.061-.768z"/></svg>',
			'pages'      => array(
				array(
					'pageType' => self::BLOG_DETAIL,
					'title'    => 'Single post',
					'uri'      => 'wp-blog-detail',
				),
			),
		),
		self::FEATURE_WP_WOOCOMMERCE => array(
			'label'   => 'WooCommerce Store',
			'feature' => self::FEATURE_WP_WOOCOMMERCE,
			'icon'    => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><path fill="#9B5C8F" d="M0 4a4 4 0 014-4h16a4 4 0 014 4v16a4 4 0 01-4 4H4a4 4 0 01-4-4V4z"/><path fill="#fff" d="M17.806 12.062c.25-.23.42-.57.515-1.03.034-.16.047-.334.047-.515 0-.202-.04-.418-.121-.634-.102-.271-.238-.417-.4-.452-.244-.049-.481.09-.705.431a2.284 2.284 0 00-.359.829 2.49 2.49 0 00-.047.508c0 .202.04.418.122.634.101.271.237.417.4.452.169.035.352-.042.548-.223z"/><path fill="#fff" d="M13.559 12.062c.25-.23.42-.57.514-1.03.034-.16.055-.334.048-.515 0-.202-.04-.418-.122-.634-.102-.271-.237-.417-.4-.452-.244-.049-.48.09-.704.431a2.283 2.283 0 00-.36.829 2.49 2.49 0 00-.047.508c0 .202.041.418.122.634.102.271.237.417.4.452.17.035.352-.042.549-.223z"/><path fill="#fff" fill-rule="evenodd" d="M3 8.25A2.25 2.25 0 015.25 6h13.5A2.25 2.25 0 0121 8.25v5.25a2.25 2.25 0 01-2.25 2.25H15l.75 2.25-4.875-2.25H5.25A2.25 2.25 0 013 13.5V8.25zm2.043-.455c-.19.014-.332.083-.426.216a.605.605 0 00-.109.466c.4 2.61.773 4.372 1.118 5.284.135.334.291.495.474.48.285-.02.623-.424 1.023-1.21.21-.446.535-1.115.976-2.006.365 1.316.867 2.304 1.497 2.966.176.188.359.271.535.258a.437.437 0 00.359-.251.846.846 0 00.081-.46c-.04-.633.02-1.517.19-2.652.176-1.17.393-2.012.657-2.513a.618.618 0 00.068-.335.55.55 0 00-.21-.396.578.578 0 00-.427-.14.52.52 0 00-.447.307c-.42.786-.718 2.06-.894 3.829a13.29 13.29 0 01-.644-2.381c-.074-.411-.257-.606-.555-.585-.204.014-.373.153-.509.418l-1.483 2.903c-.244-1.01-.474-2.242-.684-3.697-.048-.362-.244-.53-.59-.501zm13.048.501c.48.105.84.369 1.084.808.216.376.331.828.325 1.371 0 .717-.176 1.372-.529 1.97-.406.697-.934 1.045-1.592 1.045-.115 0-.237-.014-.366-.042a1.564 1.564 0 01-1.083-.808c-.217-.383-.325-.842-.325-1.378 0-.717.176-1.372.528-1.963.413-.697.942-1.045 1.592-1.045.115 0 .237.014.366.042zm-4.241 0c.474.105.84.369 1.084.808.217.376.325.828.325 1.371 0 .717-.176 1.372-.528 1.97-.407.697-.935 1.045-1.592 1.045-.116 0-.237-.014-.366-.042a1.564 1.564 0 01-1.084-.808c-.217-.383-.325-.842-.325-1.378 0-.717.176-1.372.528-1.963.413-.697.942-1.045 1.592-1.045.115 0 .237.014.366.042z" clip-rule="evenodd"/></svg>',
			'pages'   => array(
				array(
					'pageType' => self::SHOP,
					'title'    => 'Shop',
					'uri'      => 'wp-woocommerce-shop',
				),
				array(
					'pageType' => self::SINGLE_PRODUCT,
					'title'    => 'Single Product',
					'uri'      => 'wp-woocommerce-single-product',
				),
				array(
					'pageType' => self::SHOPPING_CART,
					'title'    => 'Shopping Cart',
					'uri'      => 'wp-woocommerce-shopping-cart',
				),
				array(
					'pageType' => self::CHECKOUT,
					'title'    => 'Checkout',
					'uri'      => 'wp-woocommerce-checkout',
				),
				array(
					'pageType' => self::ACCOUNT_SETTINGS,
					'title'    => 'Account Settings',
					'uri'      => 'wp-woocommerce-account-settings',
				),
			),
		),
	);

	public static function get_supported_feature_pages() {
		$supported_features = self::get_supported_features();
		$supported_features = array_filter( $supported_features );
		$supported_features = array_keys( $supported_features );

		return array_map(
			function ( $item ) {
				$feature_pages          = self::FEATURE_PAGES[ $item ];
				$feature_pages['pages'] = array_values(
					array_filter(
						$feature_pages['pages'],
						function ( $page ) {
							$page_type = $page['pageType'];

							return ! empty( self::get_feature_page_preview_url( $page_type ) );
						}
					)
				);

				return $feature_pages;
			},
			$supported_features
		);
	}

	private static function get_supported_features() {
		return array(
			self::FEATURE_WP_BLOG        => true,
			self::FEATURE_WP_WOOCOMMERCE => Woo_Commerce_Manager::is_plugin_active(),
		);
	}

	public static function get_feature_page_preview_url( $page_type ) {
		switch ( $page_type ) {
			case self::BLOG_DETAIL:
				return self::get_first_item_url( 'post' );

			case self::BLOG_OVERVIEW:
				return get_post_type_archive_link( 'post' );

			case self::SHOP:
				return self::get_wc_page_permalink( 'shop' );

			case self::SINGLE_PRODUCT:
				return Woo_Commerce_Manager::is_plugin_active() ? self::get_first_item_url( 'product' ) : null;

			case self::SHOPPING_CART:
				return self::get_wc_page_permalink( 'cart' );

			case self::CHECKOUT:
				return self::get_wc_page_permalink( 'checkout' );

			default:
				return null;
		}
	}

	private static function get_first_item_url( $page_type ) {
		$posts = get_posts(
			array(
				'post_type'   => $page_type,
				'numberposts' => 1,
			)
		);

		if ( empty( $posts ) ) {
			return null;
		}

		return get_permalink( $posts[0]->ID );
	}

	private static function get_wc_page_permalink( $page ) {
		if ( ! Woo_Commerce_Manager::is_plugin_active() ) {
			return null;
		}

		$page_id = wc_get_page_id( $page );
		if ( empty( $page_id ) ) {
			return null;
		}

		return get_permalink( $page_id );
	}
}
