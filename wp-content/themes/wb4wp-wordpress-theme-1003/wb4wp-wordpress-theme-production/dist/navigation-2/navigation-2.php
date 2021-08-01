<?php // @codingStandardsIgnoreStart ?>
<?php
use Wb4WpTheme\Managers\Customize\Customize_Settings;
?>

<nav class="wb4wp-navbar background-id_navigation navigation-2 <?php echo Customize_Settings::get_setting( 'wb4wp_header_section_fixed_navigation_bar_setting' ) ? 'sticky' : ''; ?>">
  <div class="wb4wp-wrapper">

	<?php get_template_part( 'dist/brand/brand' ); ?>

	<div class="wb4wp-right sticky-part">
	  <button class="wb4wp-navbar-button wb4wp-menu-button" aria-label="Open Menu"></button>

	  <div class="wb4wp-custom-actions">
		<?php get_template_part( 'dist/call-button/call-button' ); ?>
		<?php get_template_part( 'dist/cart-button/cart-button', '',array( 'hover_for_cart_details' => true ) ); ?>
	  </div>
	</div>
  </div>
  
  <div class="wb4wp-menu">
	<div class="wb4wp-menu-content">
	  <?php
		wp_nav_menu(
			array(
				'theme_location' => 'wb4wp',
				'container'      => false,
				'menu_class'     => 'wb4wp-menu-items',
			)
		);
		?>
	</div>
	<div class="wb4wp-custom-actions">
	  <?php get_template_part( 'dist/call-button/call-button' ); ?>
	</div>
  </div>
</nav>
