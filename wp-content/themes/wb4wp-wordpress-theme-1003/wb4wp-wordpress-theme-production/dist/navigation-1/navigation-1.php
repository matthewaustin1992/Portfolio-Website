<?php // @codingStandardsIgnoreStart ?>
<?php
use Wb4WpTheme\Managers\Customize\Customize_Settings;

$has_logo_image =
	! empty( Customize_Settings::get_setting( 'wb4wp_logo_section_url_setting' ) ) &&
	Customize_Settings::get_setting( 'wb4wp_logo_section_show_in_header_setting' );
?>

<nav class="wb4wp-navbar background-id_navigation navigation-1 
  <?php echo Customize_Settings::get_setting( 'wb4wp_header_section_fixed_navigation_bar_setting' ) ? 'sticky' : ''; ?>
  <?php echo ! Customize_Settings::get_setting( 'wb4wp_header_section_site_title_setting' ) && ! $has_logo_image ? 'no-brand' : ''; ?>
  <?php echo $has_logo_image ? 'logo-size-' . Customize_Settings::get_setting( 'wb4wp_logo_section_size_setting' ) : ''; ?>
">
  <div class="wb4wp-wrapper">

	<div class="wb4wp-left">
	  <?php if ( Customize_Settings::get_setting( 'wb4wp_header_section_social_buttons_setting' ) ) : ?>

		<?php get_template_part( 'dist/social-icons/social-icons' ); ?>
		
	  <?php endif; ?>
	</div>
	
	<?php get_template_part( 'dist/brand/brand' ); ?>

	<div class="wb4wp-right">
	  
	  <div class="wb4wp-custom-actions">
		<?php get_template_part( 'dist/call-button/call-button' ); ?>
		<?php get_template_part( 'dist/cart-button/cart-button' ); ?>
	  </div>

	  <button class="wb4wp-navbar-button wb4wp-menu-button" aria-label="Open Menu"></button>
	</div>
  </div>

  <div class="wb4wp-menu">
	<?php
	wp_nav_menu(
		array(
			'theme_location' => 'wb4wp',
			'container'      => false,
			'menu_class'     => 'wb4wp-menu-items',
		)
	);
	?>

	<?php if ( Customize_Settings::get_setting( 'wb4wp_header_section_social_buttons_setting' ) ) : ?>
		<?php get_template_part( 'dist/social-icons/social-icons' ); ?> 
	<?php endif; ?>
	
	<div class="wb4wp-custom-actions">
	  <?php get_template_part( 'dist/call-button/call-button' ); ?>
	  <?php get_template_part( 'dist/cart-button/cart-button' ); ?>
	</div>
  </div>
</nav>
