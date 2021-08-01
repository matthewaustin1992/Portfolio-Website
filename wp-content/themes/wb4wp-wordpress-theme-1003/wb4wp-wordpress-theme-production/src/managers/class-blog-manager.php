<?php

namespace Wb4WpTheme\Managers;

/**
 * Class Blog_Manager
 */
class Blog_Manager {

	/**
	 * Gets the blog overview page URL.
	 *
	 * @return string
	 */
	public static function get_overview_url() {
		return get_site_url();
	}

	/**
	 * Gets the most recent (published) post URL.
	 *
	 * @param string $post_status status of post.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_posts/
	 *
	 * @return string|null
	 */
	public static function get_most_recent_post_url( $post_status = 'publish' ) {
		$recent_posts = wp_get_recent_posts(
			array(
				'numberposts' => 1,
				'post_status' => $post_status,
			)
		);

		if ( empty( $recent_posts ) || count( $recent_posts ) === 0 ) {
			return null;
		}

		return get_permalink( current( $recent_posts )['ID'] );
	}
}
