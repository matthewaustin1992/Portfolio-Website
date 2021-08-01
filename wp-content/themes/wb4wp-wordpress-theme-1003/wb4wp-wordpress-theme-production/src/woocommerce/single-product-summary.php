<?php

use Wb4WpTheme\Managers\Customize\Customize_Settings;

/**
 * Remove the breadcrumbs
 */
add_action( 'init', 'wb4wp_remove_wc_breadcrumbs' );

function wb4wp_remove_wc_breadcrumbs() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
}

// Add Breadcrumbs to right single product section.

add_action( 'woocommerce_single_product_summary', 'wb4wp_add_wc_breadcrumbs', 0 );

function wb4wp_add_wc_breadcrumbs() {
	if ( Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_breadcrumbs_toggle_setting' ) && function_exists( 'woocommerce_breadcrumb' ) ) {
		woocommerce_breadcrumb();
	}
}

// Add additional wrapper divs.
add_action( 'woocommerce_before_single_product_summary', 'wb4wp_before_single_product_summary_start', 0 );

if ( ! function_exists( 'wb4wp_before_single_product_summary_start' ) ) {
	function wb4wp_before_single_product_summary_start() {
		if ( Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_images_setting' ) ) {
			echo "
                <div class='before-product--product-info'>
                <div class='before-product--summary-wrapper'>
            ";
		} else {
			$show_image  = Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_images_setting' );
			$image_class = $show_image ? 'single-product-no-image' : '';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "
                <div class='before-product--product-info ${image_class}'>
                <div class='before-product--summary-wrapper'>
            ";
		}
	}
}

add_action( 'woocommerce_before_single_product_summary', 'wb4wp_before_single_product_summary_end', 99 );

if ( ! function_exists( 'wb4wp_before_single_product_summary_end' ) ) {
	function wb4wp_before_single_product_summary_end() {
		echo '</div>';
	}
}

add_action( 'woocommerce_single_product_summary', 'wb4wp_single_product_summary_end', 99 );

if ( ! function_exists( 'wb4wp_single_product_summary_end' ) ) {
	function wb4wp_single_product_summary_end() {
		echo '</div>';
	}
}

add_filter( 'woocommerce_product_tabs', 'wb4wp_adjust_product_tabs', 98 );

if ( ! function_exists( 'wb4wp_adjust_product_tabs' ) ) {
	function wb4wp_adjust_product_tabs( $tabs ) {
		if ( ! Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_description_tab_toggle_setting' ) ) {
			unset( $tabs['description'] );
		}

		if ( ! Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_additional_information_tab_toggle_setting' ) ) {
			unset( $tabs['additional_information'] );
		}

		if ( ! Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_reviews_tab_toggle_setting' ) ) {
			unset( $tabs['reviews'] );
		}

		return $tabs;

	}
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'wb4wp_template_single_meta', 40 );

if ( ! function_exists( 'wb4wp_template_single_meta' ) ) {
	function wb4wp_template_single_meta() {
		if ( Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_meta_data_toggle_setting' ) && function_exists( 'woocommerce_template_single_meta' ) ) {
			woocommerce_template_single_meta();
		}
	}
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'wb4wp_single_product_description', 20 );

function wb4wp_single_product_description() {
	global $product;

	$description = null;
	if ( ! empty( $product->get_description() ) ) {
		$description = $product->get_description();
	} elseif ( ! empty( $product->get_short_description() ) ) {
		$description = $product->get_short_description();
	} else {
		return;
	}

	$description_html = apply_filters( 'woocommerce_short_description', $description );

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo "
        <div class='woocommerce-product-details__short-description'>
            {$description_html}
        </div>
    ";
}

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'wb4wp_show_product_images', 20 );
if ( ! function_exists( 'wb4wp_show_product_images' ) ) {
	function wb4wp_show_product_images() {
		if ( Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_images_setting' ) && function_exists( 'woocommerce_show_product_images' ) ) {
			woocommerce_show_product_images();
		}
	}
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product_summary', 'wb4wp_product_related_products', 19 );

if ( ! function_exists( 'wb4wp_product_related_products' ) ) {
	function wb4wp_product_related_products() {
		if ( Customize_Settings::get_setting( 'wb4wp_wc_single_product_section_show_related_products_toggle_setting' ) && function_exists( 'woocommerce_output_related_products' ) ) {
			woocommerce_output_related_products();
		}
	}
}
