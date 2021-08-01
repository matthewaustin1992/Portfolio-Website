<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Website Builder Theme
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'wb4wp_theme' ); ?></h1>
				<h2>
					<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'wb4wp_theme' ); ?>
				</h2>

				<?php get_search_form(); ?>
			</header>

			<div class="page-content">		
				<div class="widget-wrapper">
					<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

					<div class="widget widget_categories">
						<h2 class="widgettitle">
							<?php esc_html_e( 'Most Used Categories', 'wb4wp_theme' ); ?>
						</h2>
						<ul class="recent_posts_wrapper">
							<?php
							wp_list_categories(
								array(
									'orderby'    => 'count',
									'order'      => 'DESC',
									'show_count' => 1,
									'title_li'   => '',
									'number'     => 10,
								)
							);
							?>
						</ul>
					</div>

					<?php
						/* translators: %1$s: smiley */
						$_s_archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives. %1$s', 'wb4wp_theme' ), convert_smilies( ':)' ) ) . '</p>';
						the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$_s_archive_content" );
					?>
				</div>

			</div>
		</section>

	</main>

<?php
get_footer();
