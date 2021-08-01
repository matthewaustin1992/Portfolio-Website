<?php

namespace Wb4WpTheme\Managers\Customize;

use Wb4WpTheme\Managers\Customize\Controls\Toggle_Control;
use WP_Customize_Color_Control;
use WP_Customize_Manager;

/**
 * Customize controls class
 */
abstract class Customize_Controls {

	/**
	 * The {@link WP_Customize_Manager} instance.
	 *
	 * @var WP_Customize_Manager
	 */
	private $wp_customize;

	/**
	 * Constructor for customize controls
	 * @param WP_Customize_Manager $wp_customize Class instance.
	 */
	public function __construct( $wp_customize ) {
		$this->wp_customize = $wp_customize;
	}

	/**
	 * Get full section
	 *
	 * @param string $section_name name of section.
	 * @return string Name with prefix and suffix.
	 */
	public static function get_full_section_name( $section_name ) {
		return "wb4wp_{$section_name}_section";
	}

	/**
	 * Stiches full section name and section name together.
	 *
	 * @param string $full_section_name Full section name.
	 * @param string $setting_name setting name.
	 * @return string name with suffix.
	 */
	public static function get_full_setting_name( $full_section_name, $setting_name ) {
		return "{$full_section_name}_{$setting_name}_setting";
	}

	/**
	 * Registers a new setting panel in the WP_Customize_Manager.
	 *
	 * @param string  $panel_name .
	 * @param string  $title .
	 * @param string  $description .
	 * @param integer $priority the height/index the panel will be shown.
	 * @return string Full panel name, wb4wp_{$panel_name}_panel.
	 */
	protected function register_panel( $panel_name, $title, $description, $priority = 2 ) {
		$full_panel_name = "wb4wp_{$panel_name}_panel";
		$this->wp_customize->add_panel(
			$full_panel_name,
			array(
				'priority'    => $priority,
				'title'       => $title,
				'description' => $description,
			)
		);
		return $full_panel_name;
	}

	/**
	 * Registers a new section in the WP_Customize_Manager.
	 *
	 * @param string  $section_name .
	 * @param string  $title .
	 * @param string  $description .
	 * @param integer $priority the height/index the panel will be shown.
	 * @param string  $panel_name .
	 * @return string Full name of the section.
	 */
	protected function register_section( $section_name, $title, $description, $priority = 2, $panel_name = null ) {
		$full_section_name = $this->get_full_section_name( $section_name );
		$this->wp_customize->add_section(
			$full_section_name,
			array(
				'priority'    => $priority,
				'title'       => $title,
				'description' => $description,
				'panel'       => $panel_name,
			)
		);

		return $full_section_name;
	}

	/**
	 * Registers a new setting in a section
	 *
	 * @param string $section_name name of the setting.
	 * @param array  $section_settings List of settings.
	 * @return void
	 */
	protected function register_section_settings( $section_name, $section_settings ) {
		foreach ( $section_settings as $setting_name => $setting ) {
			$this->register_section_setting(
				$section_name,
				$setting_name,
				$setting
			);
		}
	}

	/**
	 * Registers a composite section
	 *
	 * @param string $section_name name of the section.
	 * @param array  $composite_section_settings array of settings.
	 */
	protected function register_composite_section_settings( $section_name, $composite_section_settings ) {
		foreach ( $composite_section_settings as $composite_section => $section_settings ) {
			$composite_section_name = "{$section_name}:{$composite_section}";
			$this->register_section_settings( $composite_section_name, $section_settings );
		}
	}

	/**
	 * Registers the setting in the section.
	 *
	 * @param string $section_name name pof the section.
	 * @param string $setting_name name of the settings.
	 * @param array  $setting array of settings.
	 */
	private function register_section_setting( $section_name, $setting_name, $setting ) {
		$this->add_setting_with_control(
			$section_name,
			$setting_name,
			$setting
		);
	}

	/**
	 * Adds a setting and a control to the Wp Theme customizer api.
	 *
	 * @param string $section_name name of the section.
	 * @param string $setting_name name fo the setting.
	 * @param array  $setting array of settings.
	 */
	private function add_setting_with_control( $section_name, $setting_name, $setting ) {
		$section_is_composite = strpos( $section_name, '_section:' ) !== false;
		if ( $section_is_composite ) {
			$composite_section_name_segments = explode( ':', $section_name );
			$section_name                    = $composite_section_name_segments[0];
			$child_section_name              = $composite_section_name_segments[1];

			$full_child_section_name = $this->get_full_section_name( $child_section_name );
			$full_setting_name       = self::get_full_setting_name( $full_child_section_name, $setting_name );
		} else {
			$full_setting_name = self::get_full_setting_name( $section_name, $setting_name );
		}

		$default_value = $setting['default'];

		switch ( $setting['type'] ) {
			case 'color':
				$this->wp_customize->add_setting(
					$full_setting_name,
					array(
						'type'              => 'option',
						'default'           => $default_value,
						'transport'         => 'refresh',
						'sanitize_callback' => 'sanitize_hex_color',
					)
				);

				$this->wp_customize->add_control(
					new WP_Customize_Color_Control(
						$this->wp_customize,
						"{$full_setting_name}_control",
						array_merge(
							$setting,
							array(
								'section'  => $section_name,
								'settings' => $full_setting_name,
							)
						)
					)
				);
				break;

			case 'toggle':
				$this->wp_customize->add_setting(
					$full_setting_name,
					array(
						'type'              => 'option',
						'default'           => $default_value,
						'transport'         => 'refresh',
						'sanitize_callback' => function ( $checked ) use ( $default_value ) {
							if ( true === $checked || 'true' === $checked ) {
								return 'true';
							}
							if ( false === $checked || 'false' === $checked ) {
								return 'false';
							}
							return $default_value;
						},
					)
				);

				$this->wp_customize->add_control(
					new Toggle_Control(
						$this->wp_customize,
						$full_setting_name,
						array_merge(
							$setting,
							array(
								'section'  => $section_name,
								'settings' => $full_setting_name,
							)
						)
					)
				);
				break;

			default:
				$this->wp_customize->add_setting(
					$full_setting_name,
					array(
						'type'      => 'option',
						'default'   => $default_value,
						'transport' => 'refresh',
					)
				);

				$this->wp_customize->add_control(
					"{$full_setting_name}_control",
					array_merge(
						$setting,
						array(
							'section'  => $section_name,
							'settings' => $full_setting_name,
						)
					)
				);
		}
	}

}
