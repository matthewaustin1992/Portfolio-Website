<?php // @codingStandardsIgnoreStart ?>
<?php
  use Wb4WpTheme\Managers\Customize\Customize_Settings;

  $layout                  = Customize_Settings::get_setting( 'wb4wp_single_post_section_layout_setting' );
  $blog_template_part_path = 'dist/blog/blog';
?>

<?php $show_tags = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_tags_setting' ); ?>
<?php $show_title_in_cover = Customize_Settings::get_setting( 'wb4wp_single_post_section_place_title_in_cover_setting' ); ?>

<content class="kv-page entry-content">
  <div class="kv-page-content">

	<?php if ( ! $show_title_in_cover ) : ?>
	  <h1 class="wp4wp-page-title page-title entry-title has-huge-font-size"><?php single_post_title(); ?></h1>
	<?php endif ?>

	<?php
	  the_content();

	wp_link_pages(
		array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wb4wp_theme' ),
			'after'  => '</div>',
		)
	);
	?>
	
	<?php if ( $layout == 'single-post-1' ) : ?> 
		<?php get_template_part( $blog_template_part_path, 'tags' ); ?>
	<?php endif ?>

	<?php if ( $layout == 'single-post-2' ) : ?> 
		<?php	get_template_part( $blog_template_part_path, 'header-meta' ); ?>
	<?php endif ?>

  </div>
</content>
