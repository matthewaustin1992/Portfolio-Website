<?php

use Wb4WpTheme\Managers\Customize\Customize_Settings;

add_filter( 'woocommerce_cart_item_subtotal', 'wb4wp_wc_show_product_discount_order_summary', 10, 3 );

function wb4wp_wc_show_product_discount_order_summary( $total, $cart_item ) {
	$product = $cart_item['data'];

	if ( '' !== $product->get_sale_price() ) {
		$regular_price    = $product->get_regular_price();
		$quantity         = $cart_item['quantity'];
		$wc_regular_price = wc_price( $regular_price * $quantity );

		$total .= "<del>{$wc_regular_price}</del>";
	}

	return $total;
}

add_filter( 'woocommerce_enable_order_notes_field', 'wb4wp_wc_enable_order_notes_field' );

if ( ! function_exists( 'wb4wp_wc_enable_order_notes_field' ) ) {
	/**
	 * @return mixed
	 */
	function wb4wp_wc_enable_order_notes_field() {
		return Customize_Settings::get_setting( 'wb4wp_wc_checkout_section_show_order_notes_toggle_setting' );
	}
}

add_action( 'woocommerce_before_checkout_form', 'wb4wp_layout_class', 5 );
if ( ! function_exists( 'wb4wp_layout_class' ) ) {
	function wb4wp_layout_class() {
		$layout = Customize_Settings::get_setting( 'wb4wp_wc_checkout_section_layout_setting' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo( "<div class='{$layout}'>" );
	}
}
add_action( 'woocommerce_after_checkout_form', 'wb4wp_layout_class_close', 5 );
if ( ! function_exists( 'wb4wp_layout_class_close' ) ) {
	function wb4wp_layout_class_close() {
		echo( '<div />' );
	}
}
