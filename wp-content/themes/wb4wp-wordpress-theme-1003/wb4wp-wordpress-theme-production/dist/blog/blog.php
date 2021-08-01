<?php // @codingStandardsIgnoreStart ?>
<?php
	use Wb4WpTheme\Managers\Customize\Customize_Settings;
	$blog_template_part_path = 'dist/blog/blog';
	$layout                  = Customize_Settings::get_setting( 'wb4wp_single_post_section_layout_setting' );

if ( $layout == 'single-post-1' ) {
	$header_image = 'header-image';
} else {
	$header_image = 'header-image-a';
}
	$header_template = has_post_thumbnail( $post ) ? $header_image : 'header';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( $blog_template_part_path, $header_template ); ?>

	<div class="wb4wp-blog-container">
		<?php get_template_part( $blog_template_part_path, 'content' ); ?>
		<?php get_template_part( $blog_template_part_path, 'footer' ); ?>

		<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() ) {
			comments_template();
		}
	?>
	</div>
</article>
