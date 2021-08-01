<?php
namespace Wb4WpTheme\Helpers;

/**
 * Font helper
 */
class Font_Helper {

	/**
	 * Font_Helper instance
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Heading fonts
	 *
	 * @var $heading_fonts
	 */
	private $heading_fonts;

	/**
	 * Body fonts
	 *
	 * @var $body_fonts
	 */
	private $body_fonts;

	/**
	 * Font Helper instance.
	 *
	 * @return Font_Helper
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Font_Helper();
			self::$instance->initialize();

		}

		return self::$instance;
	}

	/**
	 * get all heading fonts.
	 *
	 * @return array of fonts.
	 */
	public function get_heading_fonts() {
		return $this->heading_fonts;
	}

	/**
	 * Get all body fonts.
	 *
	 * @return array of fonts.
	 */
	public function get_body_fonts() {
		return $this->body_fonts;
	}

	/**
	 * Empty constructor.
	 */
	private function __construct() {
	}

	/**
	 * Initialize Font Helper.
	 *
	 * @return void
	 */
	private function initialize() {
		$font_styles_json = file_get_contents( __DIR__ . '/font-styles.json', true );
		$font_styles      = json_decode( $font_styles_json );

		$this->heading_fonts = array();
		$this->body_fonts    = array();

		$font_styles_used_in_pair = array();
		foreach ( $font_styles as $font_name => $font_config ) {
			if ( empty( $font_config->pair->name ) ) {
				continue;
			}

			$this->heading_fonts[] = array(
				'name'   => current( explode( ':', $font_name ) ),
				'weight' => $font_config->weight,
			);

			$font_styles_used_in_pair[ $font_config->pair->name ] = true;
		}

		foreach ( $font_styles as $font_name => $font_config ) {
			if ( empty( $font_styles_used_in_pair[ $font_name ] ) ) {
				continue;
			}

			$this->body_fonts[] = array(
				'name'   => current( explode( ':', $font_name ) ),
				'weight' => $font_config->weight,
			);
		}
	}

}
