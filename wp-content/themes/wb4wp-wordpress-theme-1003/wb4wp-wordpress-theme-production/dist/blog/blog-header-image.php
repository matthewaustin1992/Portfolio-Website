<?php // @codingStandardsIgnoreStart ?>
<?php
  use Wb4WpTheme\Managers\Customize\Customize_Settings;

  $blog_template_part_path = 'dist/blog/blog';
?>

<?php $show_cover_image = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_cover_image_setting' ); ?>
<?php $show_title_in_cover = Customize_Settings::get_setting( 'wb4wp_single_post_section_place_title_in_cover_setting' ); ?>

<?php if ( ! $show_cover_image && ! $show_title_in_cover ) : ?>
  <header class="entry-header wp4wp-header-image wp4wp-header-image--alternate"></header>
  <?php else : ?>
	<header class="entry-header wp4wp-header-image wp4wp-header-image">
  
	  <?php if ( $show_cover_image ) : ?>
			<?php $url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); ?>
		<img src="<?php echo $url; ?>" />
	  <?php endif ?>

	  <?php if ( $show_title_in_cover ) : ?>
		<h1 class="wp4wp-page-title page-title entry-title has-huge-font-size">
			<?php single_post_title(); ?>
		</h1>
	  <?php endif ?>

	</header>
<?php endif ?>

<div class="wb4wp-blog-container wb4wp-header-overlap">
  <?php	get_template_part( $blog_template_part_path, 'header-meta' ); ?>
</div>
