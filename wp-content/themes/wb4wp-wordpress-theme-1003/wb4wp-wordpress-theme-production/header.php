<?php

use Wb4WpTheme\Managers\Color_Manager;
use Wb4WpTheme\Managers\Customize\Customize_Settings;
use Wb4WpTheme\Managers\Template_Manager;

// phpcs:ignoreFile xss

/**
 * Get font name and weight based on setting name
 *
 * @param string $full_setting_name
 * @return void
 */
function get_font_setting( $full_setting_name ) {
	$setting  = Customize_Settings::get_setting( $full_setting_name );
	$exploded = explode( ':', $setting );
	return array(
		'font'   => $exploded[0],
		'weight' => $exploded[1],
	);
}

$fonts_body_setting    = get_font_setting( 'wb4wp_fonts_section_body_setting' );
$fonts_heading_setting = get_font_setting( 'wb4wp_fonts_section_heading_setting' );
?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<title>
		<?php
		$metadata = get_option( 'wb4wp_metadata', null );
		echo ( ! empty( $metadata ) && ! empty( $metadata['siteName'] ) ) ? $metadata['siteName'] : get_bloginfo( 'name' );
		?>
	</title>
	<style>
		/** CSS Values 2 */
		:root {
			--wb4wp-background: <?= Customize_Settings::get_setting( 'wb4wp_color_section_background_setting' ); ?>;
			--wb4wp-background-stronger: <?= Color_Manager::get_background_color_strong(); ?>;
			--wb4wp-background-strongest: <?= Color_Manager::get_background_color_stronger(); ?>;
			--wb4wp-background-lighter: <?= Color_Manager::get_background_color_lighter(); ?>;
			--wb4wp-background-on-accent2: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_accent2_setting' ); ?>;
			--wb4wp-background-on-accent2-softer: <?= Color_Manager::get_color_softer(Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_accent2_setting' )); ?>;
			--wb4wp-background-on-accent2-10: <?= Color_Manager::set_opacity_in_color( Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_accent2_setting' ), 0.1 ); ?>;
			--wb4wp-background-on-accent1: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_accent1_setting' ); ?>;
			--wb4wp-background-on-accent1-softer: <?= Color_Manager::get_color_softer(Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_accent1_setting' )); ?>;
			--wb4wp-background-on-accent1-10: <?= Color_Manager::set_opacity_in_color( Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_accent2_setting' ), 0.1 ); ?>;
			--wb4wp-background-on-text: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_text_setting' ); ?>;
			--wb4wp-background-on-text-10: <?= Color_Manager::set_opacity_in_color( Color_Manager::get_text_color_on_background( 'wb4wp_color_section_background_setting', 'wb4wp_color_section_text_setting' ), 0.1); ?>;

			--wb4wp-text-color: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_text_setting', null ); ?>;
			--wb4wp-text-color-softer: <?= Color_Manager::get_color_softer( null ); ?>;
			--wb4wp-text-color-stronger: <?= Color_Manager::get_text_color_stronger(); ?>;
			--wb4wp-text-color-10: <?= Color_Manager::get_text_color_with_opacity(0.1); ?>;
			--wb4wp-text-color-20: <?= Color_Manager::get_text_color_with_opacity(0.2); ?>;
			--wb4wp-text-color-75: <?= Color_Manager::get_text_color_with_opacity(0.75); ?>;
			--wb4wp-text-color-contrast: rgb(255, 255, 255);

			--wb4wp-accent1: <?= Color_Manager::get_background_color_by_name( 'wb4wp_color_section_accent1_setting', null ); ?>;
			--wb4wp-accent1-on-background: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent1_setting', 'wb4wp_color_section_background_setting' ); ?>;
			--wb4wp-accent1-on-text: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent1_setting', 'wb4wp_color_section_text_setting' ); ?>;
			--wb4wp-text-accent1: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_text_setting', 'wb4wp_color_section_accent1_setting' ); ?>;
			--wb4wp-text-accent1-softer: <?= Color_Manager::get_color_softer( Color_Manager::get_text_color_on_background( 'wb4wp_color_section_text_setting', 'wb4wp_color_section_accent1_setting' ) ); ?>;
			--wb4wp-accent1-stronger: <?= Color_Manager::get_color_stronger( Color_Manager::get_background_color_by_name( 'wb4wp_color_section_accent1_setting', null ) ); ?>;
			--wb4wp-title-accent1: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent2_setting', 'wb4wp_color_section_accent1_setting' ); ?>;
			--wb4wp-title-accent1-text: <?= Color_Manager::get_text_color_for_color( Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent2_setting', 'wb4wp_color_section_accent1_setting' ) ); ?>;

			--wb4wp-accent2: <?= Color_Manager::get_background_color_by_name( 'wb4wp_color_section_accent2_setting', null ); ?>;
			--wb4wp-text-accent2: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent2_setting', null ); ?>;
			--wb4wp-text-accent2-softer: <?= Color_Manager::get_color_softer( Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent2_setting', null ) ); ?>;
			--wb4wp-accent2-stronger: <?= Color_Manager::get_color_stronger( Color_Manager::get_background_color_by_name( 'wb4wp_color_section_accent2_setting', null ) ); ?>;
			--wb4wp-title-accent2: <?= Color_Manager::get_text_color_on_background( 'wb4wp_color_section_accent2_setting', 'wb4wp_color_section_accent2_setting' ); ?>;

			--wb4wp-primary-color: <?= Color_Manager::get_primary_color(); ?>;
			--wb4wp-primary-color-text: <?= Color_Manager::get_primary_color_text(); ?>;
			--wb4wp-primary-color-light: <?= Color_Manager::get_primary_color_light(); ?>;
			--wb4wp-primary-color-lighter: <?= Color_Manager::get_primary_color_lighter(); ?>;
			--wb4wp-primary-color-lightest: <?= Color_Manager::get_primary_color_lightest(); ?>;
			--wb4wp-primary-color-border: <?= Color_Manager::get_primary_color_border(); ?>;
			--wb4wp-primary-color-stronger: <?= Color_Manager::get_primary_color_stronger(); ?>;

			--wb4wp-border-color: <?= Color_Manager::get_border_color(); ?>;

			--wb4wp-font-body: '<?= $fonts_body_setting['font']; ?>';
			--wb4wp-font-body-weight: <?= $fonts_body_setting['weight']; ?>;
			--wb4wp-font-heading: '<?= $fonts_heading_setting['font']; ?>';
			--wb4wp-font-heading-weight: <?= $fonts_heading_setting['weight']; ?>;
			--wb4wp-font-size-override: <?= ( (float) Customize_Settings::get_setting( 'wb4wp_fonts_section_font_size_setting' ) * 100 ) . '%'; ?>;
			--wb4wp-font-size-factor: <?= (float) Customize_Settings::get_setting( 'wb4wp_fonts_section_font_size_setting' ); ?>;

		}

		body #page.kv-site .kv-page-content {
			--kv-ee-global-font-size-factor: var(--wb4wp-font-size-factor);
			--kv-ee-heading-font-family: var(--wb4wp-font-heading);
			--kv-ee-heading-font-weight: var(--wb4wp-font-heading-weight);
			--kv-ee-body-font-family: var(--wb4wp-font-body);
			--kv-ee-body-font-weight: var(--wb4wp-font-body-weight);
		}
	</style>

	<link rel='stylesheet' href='https://fonts.googleapis.com/css?display=swap&family=<?php echo str_replace( ' ', '+', Customize_Settings::get_setting( 'wb4wp_fonts_section_body_setting' ) ); ?>|<?php echo str_replace( ' ', '+', Customize_Settings::get_setting( 'wb4wp_fonts_section_heading_setting' ) ); ?>' />

	<?php wp_head(); ?>

	<?php
	$post = get_post();
	?>

	<link href="https://components.mywebsitebuilder.com/fonts/font-awesome.css" rel="stylesheet">
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php echo Template_Manager::get_header(); ?>

	<div id="page" class="site kv-site kv-main">
