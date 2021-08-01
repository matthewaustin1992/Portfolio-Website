<?php

namespace Wb4Wp\Managers;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Woo_Commerce_Manager
 * @package Wb4Wp\Managers
 */
class Woo_Commerce_Manager {

	const WOO_COMMERCE_PLUGIN_PATH = 'woocommerce/woocommerce.php';

	private $wc_currency_symbol;

	/**
	 * Gets WooCommerce products. Supports pagination.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_products( $request ) {
		if ( ! self::is_plugin_active() ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'The WooCommerce plugin is not active.',
				),
				500
			);
		}

		$page     = 1;
		$per_page = 10;
		$category = array();

		if ( isset( $request['page'] ) ) {
			$page = (int) $request['page'];
		}

		if ( isset( $request['per_page'] ) ) {
			$per_page = (int) $request['per_page'];
		}

		if ( isset( $request['category'] ) ) {
			$category = explode( ',', $request['category'] );
		}

		$products_query_args = array(
			'status'   => 'publish',
			'paginate' => true,
			'limit'    => $per_page,
			'page'     => $page,
		);

		if ( ! empty( $category ) ) {
			$products_query_args['category'] = $category;
		}

		$response = wc_get_products( $products_query_args );

		$response->success  = true;
		$response->page     = $page;
		$response->products = array_map(
			array( $this, 'to_product_dto' ),
			$response->products
		);

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Checks if the WooCommerce plugin is active.
	 *
	 * @return boolean Whether WooCommerce is installed
	 */
	public static function is_plugin_active() {
		return is_plugin_active( self::WOO_COMMERCE_PLUGIN_PATH );
	}

	/**
	 * Gets a WooCommerce product.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_product( $request ) {
		if ( ! self::is_plugin_active() ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'The WooCommerce plugin is not active.',
				),
				500
			);
		}

		$wc_product = wc_get_product( $request['id'] );

		if ( empty( $wc_product ) ) {
			return new WP_REST_Response( array( 'success' => false ), 404 );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'product' => $this->to_product_dto( $wc_product ),
			),
			200
		);
	}

	private function to_product_dto( $wc_product ) {
		$product_dto                    = $wc_product->get_data();
		$product_dto['name']            = wp_strip_all_tags( $product_dto['name'] );
		$product_dto['description']     = wp_strip_all_tags( $product_dto['description'] );
		$product_dto['image_url']       = wp_get_attachment_url( $wc_product->get_image_id() );
		$product_dto['currency_symbol'] = $this->get_wc_currency_symbol();

		return $product_dto;
	}

	private function get_wc_currency_symbol() {
		if ( empty( $this->wc_currency_symbol ) ) {
			$this->wc_currency_symbol = get_woocommerce_currency_symbol();
		}

		return $this->wc_currency_symbol;
	}

}
