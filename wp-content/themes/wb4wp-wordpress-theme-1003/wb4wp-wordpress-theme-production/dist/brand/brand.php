<?php // @codingStandardsIgnoreStart ?>
<?php
  use Wb4WpTheme\Managers\Customize\Customize_Settings;

  // Defaults
  $template_args = array(
	  'section'   => 'header',
	  'show_logo' => false,
  );

  if ( ! empty( $args ) ) {
	  $template_args = array_merge( $template_args, $args );
  }

  $has_logo_image =
	! empty( Customize_Settings::get_setting( 'wb4wp_logo_section_url_setting' ) ) &&
	Customize_Settings::get_setting( 'wb4wp_logo_section_show_in_' . $template_args['section'] . '_setting' );
	?>

<div class="wb4wp-brand <?php echo $has_logo_image ? 'has-image' : ''; ?>">
  <?php
	if ( Customize_Settings::get_setting( 'wb4wp_' . $template_args['section'] . '_section_site_title_setting' ) || $has_logo_image
	) :
		?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Home" rel="home">
		<?php if ( $has_logo_image ) : ?>
			<?php $logo_size = Customize_Settings::get_setting( 'wb4wp_logo_section_size_setting' ); ?>
		<div class="wb4wp-image <?php echo $logo_size; ?>">
		  <img src="<?php echo Customize_Settings::get_setting( 'wb4wp_logo_section_url_setting' ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>">
		</div>
	  <?php endif; ?>

		<?php if ( Customize_Settings::get_setting( 'wb4wp_' . $template_args['section'] . '_section_site_title_setting' ) ) : ?>
		<div class="wb4wp-text">
			<?php /* Use the regular bloginfo() function to keep applied html in the sitetitle from other editors*/ ?>
			<?php bloginfo( 'name' ); ?>
		</div>
	  <?php endif; ?>

	</a>
  <?php endif; ?>
</div>
