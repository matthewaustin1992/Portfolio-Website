<?php

namespace Wb4Wp\Managers;

use Wb4Wp\Helpers\Base64_Helper;

/**
 * Class Gutenberg_Manager
 * @package Wb4Wp\Managers
 */
class Gutenberg_Manager {

	/**
	 * Generates the actual gutenberg block
	 *
	 * @param array $section
	 * @param array $section_data
	 * @param array $additional_params
	 *
	 * @return string|null
	 */
	public static function generate_gutenberg_block( $section, $section_data, $additional_params = array() ) {
		if ( empty( $section_data['html'] ) || empty( $section_data['css'] ) ) {
			return null;
		}

		if ( isset( $section_data['isNativeGutenberg'] ) && true === $section_data['isNativeGutenberg'] ) {
			return self::generate_native_block( $section, $section_data );
		}

		return self::generate_raw_section_block( $section, $section_data, $additional_params );
	}

	private static function generate_native_block( $section, $section_data ) {
		$section_params = array(
			'id'           => $section_data['id'],
			'backgroundId' => $section_data['backgroundId'],
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			'background'   => base64_encode( wp_json_encode( $section['binding']['background'] ) ),
		);

		return '
			<!-- wp:wb4wp/container ' . wp_json_encode( $section_params ) . ' -->
		    <div class="wp-block-wb4wp-container ' . $section_data['backgroundId'] . '">
		        <div class="wp-block-wb4wp-container__background">' . $section_data['backgroundHTML'] . '</div>
		        <div class="wp-block-wb4wp-container__inner-container kv-content wp-embed-responsive">
					<div class="kv-ee-container">' . $section_data['html'] . '</div>
				</div>
		    </div>
		    <!-- /wp:wb4wp/container -->
	    ';
	}

	private static function generate_raw_section_block( $section, $section_data, $additional_params ) {
		$section_html = $section_data['html'];
		$section_css  = $section_data['css'];

		$section_params = $section;
		if ( ! empty( $section_params['headerSection'] ) ) {
			unset( $section_params['headerSection'] );
		}

		$html = "<!-- wp:html -->$section_html<style scoped>$section_css</style><!-- /wp:html -->";

		$section_params = array_merge_recursive( $section_params, $additional_params );

		foreach ( array( 'layout', 'binding' ) as $property_to_encode ) {
			if ( empty( $section_params[ $property_to_encode ] ) ) {
				continue;
			}

			$property = &$section_params[ $property_to_encode ];
			$property = Base64_Helper::base64_encode_recursive( $property );
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		return '<!-- wp:wb4wp/block-generic-section ' . json_encode( $section_params ) . ' -->' . $html . '<!-- /wp:wb4wp/block-generic-section -->';
	}

}
