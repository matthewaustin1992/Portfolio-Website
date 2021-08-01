<?php

namespace Wb4WpTheme\Managers;

/**
 * WordPress Manager Class
 */
class WordPress_Manager {

	/**
	 * Retrieves sitemap
	 */
	public static function has_sitemap() {
		return get_option( 'blog_public' );
	}

	/**
	 * Retrieves sitemap url
	 */
	public static function get_sitemap_url() {
		return get_site_url( null, '/sitemap.xml' );
	}
}
