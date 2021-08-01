<?php // @codingStandardsIgnoreStart ?>
<?php

use Wb4WpTheme\Managers\Customize\Customize_Settings;
use Wb4WpTheme\Managers\WordPress_Manager;
use Wb4WpTheme\Helpers\Template_Helper;

$footer_color_string = Template_Helper::get_color_rules_by_index( 'wb4wp_footer_section_background_color_index_setting', array(
	// Third color selection in the editor
	[
			'--wb4wp-footer-background' => '--wb4wp-background',
			'--wb4wp-footer-text' => '--wb4wp-text-color',
			'--wb4wp-footer-text-softer' => '--wb4wp-text-color-softer',
			'--wb4wp-footer-contact-link-color' => '--wb4wp-text-accent1',
			'--wb4wp-footer-border-color' => '--wb4wp-border-color'
	],
	// Fourth color selection in the editor
	[
			'--wb4wp-footer-background' => '--wb4wp-text-color',
			'--wb4wp-footer-text' => '--wb4wp-background',
			'--wb4wp-footer-text-softer' => '--wb4wp-background-stronger',
			'--wb4wp-footer-contact-link-color' => '--wb4wp-text-accent2',
			'--wb4wp-footer-border-color' => '--wb4wp-background-stronger'
	],
	// First color selection in the editor
	[
			'--wb4wp-footer-background' => '--wb4wp-accent1',
			'--wb4wp-footer-text' => '--wb4wp-background-on-accent1',
			'--wb4wp-footer-text-softer' => '--wb4wp-background-on-accent1-softer',
			'--wb4wp-footer-contact-link-color' => '--wb4wp-text-accent2',
			'--wb4wp-footer-border-color' => '--wb4wp-border-color'
	],
	// Second color selection in the editor
	[
			'--wb4wp-footer-background' => '--wb4wp-accent2',
			'--wb4wp-footer-text' => '--wb4wp-background-on-accent2',
			'--wb4wp-footer-text-softer' => '--wb4wp-background-on-accent2-softer',
			'--wb4wp-footer-contact-link-color' => '--wb4wp-text-accent1',
			'--wb4wp-footer-border-color' => '--wb4wp-border-color-contrast'
	]
));
?>
<footer class="footer-3 wb4wp-footer" style="<?= $footer_color_string ?>">
	<?php 
		if( 
			!empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_pattern_index_setting' )) ||
			!empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_video_setting' )) || 
			!empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_image_setting' ))
		) {
			get_template_part( 'dist/footer-background/footer-background' );
		}
	?>
	<div class="wb4wp-container kv-main-container">
		<div class="wb4wp-footer-section wb4wp-footer-body">

			<?php if ( ! empty( Customize_Settings::get_setting( 'wb4wp_footer_section_description_setting' ) ) ) : ?>
				<div class="wb4wp-info-block wb4wp-about-block">
					<h4 class="wb4wp-title">
						<?php Customize_Settings::get_setting( 'wb4wp_footer_section_site_title_setting' ) ? bloginfo( 'name' ) : ''; ?>
					</h4>
					<?php
					$show_description = Customize_Settings::get_setting( 'wb4wp_footer_section_description_toggle_setting' );
					$description      = Customize_Settings::get_setting( 'wb4wp_footer_section_description_setting' );
					?>
					<?php if ( $show_description && ! empty( $description ) && $description !== 'Add a description here.' ) : ?>
						<p class="wb4wp-copy">
							<?php echo $description; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_page_menu_setting' ) ) : ?>
				<div class="wb4wp-info-block">
					<h4 class="wb4wp-title">
						Pages
					</h4>
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
				</div>
			<?php endif; ?>

			<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_social_buttons_setting' ) ) : ?>
				<div class="wb4wp-info-block">
					<h4 class="wb4wp-title">
						Follow us
					</h4>
					<?php
					get_template_part(
						'dist/social-icons/social-icons',
						'',
						array(
							'show_name'     => true,
							'wrapper_class' => 'wb4wp-footer-social',
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>

		<div class="wb4wp-footer-section">
			<?php
			$show_address = Customize_Settings::get_setting( 'wb4wp_footer_section_address_toggle_setting' );

			$has_address =
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_street_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_zip_code_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_city_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_state_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_country_setting' ) );

			$show_contact = 
				Customize_Settings::get_setting( 'wb4wp_footer_section_email_setting' ) || 
				Customize_Settings::get_setting( 'wb4wp_footer_section_phone_setting' );

			$has_contact =
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_phone_number_setting' ) ) ||
				! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_email_setting' ) );

			if ( ( $show_address && $has_address ) || ( $has_contact && $show_contact ) ) :
				?>
				<div class="wb4wp-address">
					<?php if ( $show_address && $has_address ) : ?>
						<div class="wb4wp-info-block">
							<address>
								<p class="wb4wp-copy">
									<?php echo ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_street_setting' ) ) ? Customize_Settings::get_setting( 'wb4wp_contact_information_section_street_setting' ) : ''; ?>
									, <br>
									<?php echo ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_state_setting' ) ) ? Customize_Settings::get_setting( 'wb4wp_contact_information_section_state_setting' ) : ''; ?>
									<?php echo ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_zip_code_setting' ) ) ? Customize_Settings::get_setting( 'wb4wp_contact_information_section_zip_code_setting' ) : ''; ?>
									<br>
									<?php echo ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_city_setting' ) ) ? Customize_Settings::get_setting( 'wb4wp_contact_information_section_city_setting' ) : ''; ?>
									<?php echo ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_country_setting' ) ) ? Customize_Settings::get_setting( 'wb4wp_contact_information_section_country_setting' ) : ''; ?>
								</p>
							</address>
						</div>
					<?php endif; ?>

					<?php if ( $has_contact && $show_contact ) : ?>
						<div class="wb4wp-info-block wb4wp-contact">
							<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_phone_setting' ) && ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_phone_number_setting' ) ) ) : ?>
								<a class="wb4wp-contact-link wb4wp-copy"
								   href="tel:<?php echo Customize_Settings::get_setting( 'wb4wp_contact_information_section_phone_number_setting' ); ?>"><?php echo Customize_Settings::get_setting( 'wb4wp_contact_information_section_phone_number_setting' ); ?></a>
							<?php endif; ?>
							<?php if ( ! empty( Customize_Settings::get_setting( 'wb4wp_contact_information_section_email_setting' ) ) ) : ?>
								<a class="wb4wp-contact-link wb4wp-copy"
								   href="mailto:<?php echo Customize_Settings::get_setting( 'wb4wp_contact_information_section_email_setting' ); ?>"><?php echo Customize_Settings::get_setting( 'wb4wp_contact_information_section_email_setting' ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>
			<div class="wb4wp-colophon">
				<?php if ( Customize_Settings::get_setting( 'wb4wp_footer_section_copyright_message_setting' ) ) : ?>
					<p class="wb4wp-copyright">
						&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>
					</p>
				<?php endif; ?>
				<?php if ( WordPress_Manager::has_sitemap() && Customize_Settings::get_setting( 'wb4wp_footer_section_link_to_sitemap_setting' ) ) : ?>
					<nav class="wb4wp-footer-nav">
						<ul class="wb4wp-footer-menu-items">
							<li class="menu-item">
								<a href="<?php echo WordPress_Manager::get_sitemap_url(); ?>">sitemap</a>
							</li>
						</ul>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>
