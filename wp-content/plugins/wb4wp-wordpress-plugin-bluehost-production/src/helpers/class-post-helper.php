<?php

namespace Wb4Wp\Helpers;

use Wb4Wp\Managers\Gutenberg_Manager;

/**
 * Class Post_Helper
 * @package Wb4Wp\Helpers
 */
class Post_Helper {

	public static function is_built_with_wb4wp( $post_id ) {
		return get_post_meta( $post_id, 'WB4WP_EDIT_MODE', true );
	}

	/**
	 * Builds the post content based on sections provided
	 *
	 * @param array $ee_page
	 * @param array $published_section_data
	 * @param array $additional_section_params
	 *
	 * @return string|null
	 */
	public static function generate_post_content( $page, $one_page_published_section_data ) {
		if ( empty( $page['sections'] ) ) {
			return null;
		}

		$post_content = '';

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		foreach ( $page['sections'] as $section ) {
			// todo this should log an error, it mean that a section was missing from the published_pages object. (bug in the express editor).
			if ( empty( $one_page_published_section_data[ $section['id'] ] ) ) {
				continue;
			}

			// published section data for this one block.
			$one_section_data = $one_page_published_section_data[ $section['id'] ];
			$content          = Gutenberg_Manager::generate_gutenberg_block( $section, $one_section_data );
			if ( empty( $content ) ) {
				continue;
			}

			$post_content .= $content;
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		return $post_content;
	}

	public static function save_post_header( $post_id, $ee_page ) {
		if ( empty( $ee_page['sections'] ) || empty( current( $ee_page['sections'] )['html'] ) ) {
			return;
		}

		$section_html = current( $ee_page['sections'] )['html'];

		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		if ( true === $ee_page['mainPage'] ) {
			update_option( 'WB4WP_GLOBAL_HEADER', base64_encode( $section_html ) );
		}

		update_post_meta( $post_id, 'WB4WP_PAGE_HEADER', base64_encode( $section_html ) );

		// phpcs:enable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	public static function save_post_footer( $post_id, $ee_page ) {
		if ( empty( $ee_page['sections'] ) || empty( end( $ee_page['sections'] )['html'] ) || true !== $ee_page['mainPage'] ) {
			return;
		}

		$section_html = end( $ee_page['sections'] )['html'];

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		update_option( 'WB4WP_GLOBAL_FOOTER', base64_encode( $section_html ) );
		update_post_meta( $post_id, 'WB4WP_PAGE_FOOTER', base64_encode( $section_html ) );

		// phpcs:enable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
	}

	public static function has_unpublished_changes( $post_id ) {
		return wp_get_post_autosave( $post_id ) !== false;
	}

}
