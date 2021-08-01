<?php

namespace Wb4Wp\Mappers;

use Wb4Wp\Integrations\Exceptions\Theme_Not_Active_Exception;
use Wb4Wp\Integrations\Theme_Integration;
use WP_Customize_Manager;

/**
 * Class Theme_Options_Mapper
 * @package Wb4Wp\Mappers
 */
final class Theme_Options_Mapper {

	/**
	 * Maps theme settings to its DTO (Data Transfer Object)
	 *
	 * @param array $theme_settings .
	 * @param string $changeset_uuid .
	 *
	 * @return array
	 */
	public static function to_dto( $theme_options, $changeset_uuid = null ) {
		$dto            = array();
		$changeset_data = array();

		if ( null !== $changeset_uuid ) {
			$customizer     = new WP_Customize_Manager( array( 'changeset_uuid' => $changeset_uuid ) );
			$changeset_data = $customizer->changeset_data();
		}

		foreach ( $theme_options as $section_name => $settings ) {
			$dto[ $section_name ] = array();
			foreach ( $settings as $setting_name => $setting ) {
				$dto[ $section_name ][ $setting_name ] = array();

				foreach ( array( 'type', 'choices', 'label', 'description' ) as $property ) {
					if ( isset( $setting[ $property ] ) ) {
						$dto[ $section_name ][ $setting_name ][ $property ] = $setting[ $property ];
					}
				}

				self::append_id_and_value( $section_name, $setting_name, $dto[ $section_name ][ $setting_name ] );

				if ( ! empty( $changeset_data ) ) {
					$full_section_name       = $dto[ $section_name ][ $setting_name ]['_id'];
					$changeset_setting_value = ! empty( $changeset_data[ $full_section_name ] ) ? $changeset_data[ $full_section_name ] : null;

					if ( $changeset_setting_value ) {
						$dto[ $section_name ][ $setting_name ]['value'] = $changeset_setting_value['value'];
					}
				}
			}
		}

		return $dto;
	}

	/**
	 * Appends information to the DTO entry.
	 *
	 * @param string $section_name .
	 * @param string $setting_name .
	 * @param array $dto_entry .
	 */
	private static function append_id_and_value( $section_name, $setting_name, &$dto_entry ) {
		try {
			$full_setting_name = Theme_Integration::get_full_setting_name( $section_name, $setting_name );
			$value             = Theme_Integration::get_customize_setting( $full_setting_name );

			if ( 'true' === $value ) {
				$value = true;
			} elseif ( 'false' === $value ) {
				$value = false;
			}

			$dto_entry['_id']   = $full_setting_name;
			$dto_entry['value'] = $value;
		} catch ( Theme_Not_Active_Exception $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		}
	}

}
