<?php

use Wb4WpTheme\Helpers\Template_Helper;
use Wb4WpTheme\Managers\Customize\Customize_Settings;
use Wb4WpTheme\Managers\WordPress_Manager;

$footer_color_string = Template_Helper::get_color_rules_by_index(
	'wb4wp_footer_section_background_color_index_setting',
	array(
		// Third color selection in the editor.
		array(
			'--wb4wp-footer-background'          => '--wb4wp-background',
			'--wb4wp-footer-background-stronger' => '--wb4wp-background-stronger',
			'--wb4wp-footer-text'                => '--wb4wp-text-color',
			'--wb4wp-footer-text-softer'         => '--wb4wp-text-color-softer',
			'--wb4wp-footer-border-color'        => '--wb4wp-accent1',
		),
		// Fourth color selection in the editor.
		array(
			'--wb4wp-footer-background'          => '--wb4wp-text-color-stronger',
			'--wb4wp-footer-background-stronger' => '--wb4wp-text-color',
			'--wb4wp-footer-text'                => '--wb4wp-background',
			'--wb4wp-footer-text-softer'         => '--wb4wp-background-stronger',
			'--wb4wp-footer-border-color'        => '--wb4wp-accent1',
		),
		// First color selection in the editor.
		array(
			'--wb4wp-footer-background'          => '--wb4wp-accent1',
			'--wb4wp-footer-background-stronger' => '--wb4wp-accent1-stronger',
			'--wb4wp-footer-text'                => '--wb4wp-background-on-accent1',
			'--wb4wp-footer-text-softer'         => '--wb4wp-background-on-accent1-softer',
			'--wb4wp-footer-border-color'        => '--wb4wp-accent2',
		),
		// Second color selection in the editor.
		array(
			'--wb4wp-footer-background'          => '--wb4wp-accent2',
			'--wb4wp-footer-background-stronger' => '--wb4wp-accent2-stronger',
			'--wb4wp-footer-text'                => '--wb4wp-background-on-accent2',
			'--wb4wp-footer-text-softer'         => '--wb4wp-background-on-accent2-softer',
			'--wb4wp-footer-border-color'        => '--wb4wp-accent1',
		),
	)
);

?>
<footer class="footer-2 wb4wp-footer" style="<?php echo esc_attr( $footer_color_string ); ?>">
	<div class="wb4wp-container-fluid wb4wp-footer-container">
		<div class="wb4wp-footer-body">
			<div class="wb4wp-content">
				<div class="wb4wp-footer-header">

					<?php get_template_part( 'dist/brand/brand', '', array( 'section' => 'footer' ) ); ?>

					<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_page_menu_setting' ) ) : ?>
						<nav class="wb4wp-footer-nav">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'wb4wp',
									'container'      => false,
									'menu_class'     => 'wb4wp-footer-menu-items',
									'depth'          => 2,
								)
							);
							?>
						</nav>
					<?php endif; ?>
				</div>

				<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_social_buttons_setting' ) ) : ?>
					<div class="wb4wp-footer-social">
						<h3 class="wb4wp-title">
							Follow us
						</h3>
						<?php get_template_part( 'dist/social-icons/social-icons' ); ?>
					</div>
				<?php endif; ?>

				<div class="wb4wp-colophon">
					<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_copyright_message_setting' ) ) : ?>
						<p class="wb4wp-copyright">
							&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
						</p>
					<?php endif; ?>
					<?php if ( WordPress_Manager::has_sitemap() && Customize_Settings::get_setting( 'wb4wp_footer_section_link_to_sitemap_setting' ) ) : ?>
						<nav class="wb4wp-footer-nav">
							<ul class="wb4wp-footer-menu-items">
								<li class="menu-item">
									<a href="<?php echo esc_url( WordPress_Manager::get_sitemap_url() ); ?>">sitemap</a>
								</li>
							</ul>
						</nav>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="wb4wp-footer-section-empty">
			<?php
			if ( ! empty( Customize_Settings::get_setting( 'wb4wp_footer_section_background_pattern_index_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_footer_section_background_video_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_footer_section_background_image_setting' ) )
				) {
				get_template_part( 'dist/footer-background/footer-background' );
			}
			?>
		</div>
	</div>
</footer>
