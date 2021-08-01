<?php

use Wb4WpTheme\Managers\Customize\Customize_Settings;

add_filter( 'woocommerce_cart_item_thumbnail', 'wb4wp_wc_remove_item_thumbnail' );

if ( ! function_exists( 'wb4wp_wc_remove_item_thumbnail' ) ) {
	/**
	 * @param mixed $thumbnail The thumbnail.
	 *
	 * @return mixed
	 */
	function wb4wp_wc_remove_item_thumbnail( $thumbnail ) {
		$show_product_image = Customize_Settings::get_setting( 'wb4wp_wc_shopping_cart_section_show_product_image_toggle_setting' );
		if ( ! $show_product_image ) {
			return false;
		}

		return $thumbnail;
	}
}

add_filter( 'woocommerce_coupons_enabled', 'wb4wp_wc_cart_coupon' );

if ( ! function_exists( 'wb4wp_wc_cart_coupon' ) ) {
	/**
	 * @return mixed
	 */
	function wb4wp_wc_cart_coupon() {
		return Customize_Settings::get_setting( 'wb4wp_wc_shopping_cart_section_show_coupon_field_toggle_setting' );
	}
}
