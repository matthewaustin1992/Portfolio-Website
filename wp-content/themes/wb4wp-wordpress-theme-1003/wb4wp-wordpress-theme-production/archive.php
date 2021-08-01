<?php // @codingStandardsIgnoreStart ?>
<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Website Builder Theme
 */
?>

<?php get_header(); ?>

<main id="primary" class="site-main">
	<content class="kv-page entry-content">
		<div class="kv-page-content wb4wp-container wb4wp-archive">
			<?php if ( have_posts() ) : ?>
			<div class="wb4wp-content-wrapper">
				<header class="page-header">
					<?php
						the_archive_title( '<h1 class=""wp4wp-page-title page-title entry-title has-huge-font-size">', '</h1>' );
						the_archive_description( '<div class="archive-description">', '</div>' );
					?>
				</header>
			</div>

			<ul class="wb4wp-archive-<?php echo get_post_type(); ?>">
				<?php
					/* Start the Loop */
				while ( have_posts() ) {
					echo '<li class="wb4wp-archive-item wb4wp-archive-item-' . get_post_type() . '">';

					the_post();
					get_template_part( 'dist/content/content', get_post_type(), array( 'archive' => true ) );

					echo '</li>';
				}

				?>
			</ul>
			

			<?php the_posts_navigation(); ?>

			<?php else : ?>

				<?php get_template_part( 'dist/content/content-none' ); ?>			
			<?php endif; ?>
		</div>
	</content>
</main><!-- #main -->

<?php get_footer(); ?>
