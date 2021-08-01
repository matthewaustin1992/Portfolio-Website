<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Website Builder Theme
 */

get_header();
?>

	<main id="primary" class="site-main">
		<header>
			<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
		</header>
		<?php	get_template_part( 'dist/content/content', get_post_type() ); ?>
	</main><!-- #main -->

<?php
get_footer();
