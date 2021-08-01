<?php

namespace Wb4Wp\Managers;

use Wb4Wp\Models\Blog_Post;
use WP_REST_Response;

/**
 * Class Blog_Manager
 * @package Wb4Wp\Managers
 */
final class Blog_Manager {

	/**
	 * Fetches all blog categories.
	 * Pagination is possible.
	 *
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_categories( $request ) {
		$default_page_number    = 1;
		$default_items_per_page = 5;

		$paged        = ( $request->get_param( 'page' ) ) ? intval( $request->get_param( 'page' ) ) : $default_page_number;
		$per_page     = ( $request->get_param( 'per_page' ) ) ? intval( $request->get_param( 'per_page' ) ) : $default_items_per_page;
		$paged_offset = ( $paged - 1 ) * $per_page;

		$args = array(
			'order_by'   => 'name',
			'paged'      => $paged,
			'number'     => $per_page,
			'hide_empty' => false,
			'offset'     => $paged_offset,
		);

		$categories       = array_values( get_categories( $args ) );
		$total_categories = count(
			get_categories(
				array(
					'order_by'   => 'name',
					'hide_empty' => false,
				)
			)
		);

		return new WP_REST_Response(
			array(
				'items'          => $categories,
				'total_items'    => $total_categories,
				'total_pages'    => ceil( $total_categories / $per_page ),
				'current_page'   => $paged,
				'items_per_page' => $per_page,
			),
			200
		);
	}

	/**
	 * Fetches all blog posts.
	 * Pagination and filtering by category is possible.
	 *
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_blog_posts( $request ) {
		$default_page_number    = 1;
		$default_items_per_page = 10;
		$default_category       = array( 0 );

		$paged        = ( $request->get_param( 'page' ) ) ? intval( $request->get_param( 'page' ) ) : $default_page_number;
		$per_page     = ( $request->get_param( 'per_page' ) ) ? intval( $request->get_param( 'per_page' ) ) : $default_items_per_page;
		$categories   = ( $request->get_param( 'category' ) ) ? explode( ',', $request->get_param( 'category' ) ) : $default_category;
		$paged_offset = ( $paged - 1 ) * $per_page;

		$args = array(
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'offset'         => $paged_offset,
			'post_type'      => 'post',
			'category'       => $categories,
		);

		$posts = get_posts( $args );

		$post_data = array();
		foreach ( $posts as $post ) {
			array_push( $post_data, new Blog_Post( $post ) );
		}

		$total_pages = count(
			get_posts(
				array(
					'post_type' => 'post',
					'category'  => $categories,
				)
			)
		);

		return new WP_REST_Response(
			array(
				'items'          => $post_data,
				'total_items'    => $total_pages,
				'total_pages'    => ceil( $total_pages / $per_page ),
				'current_page'   => $paged,
				'items_per_page' => $per_page,
			),
			200
		);
	}

	/**
	 * Gets a single blog post.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_blog_post( $request ) {
		$blog_post = get_post( $request['id'] );

		if ( empty( $blog_post ) ) {
			return new WP_REST_Response( array( 'success' => false ), 404 );
		}

		return new WP_REST_Response(
			array(
				'success'   => true,
				'blog_post' => new Blog_Post( $blog_post ),
			),
			200
		);
	}
}
