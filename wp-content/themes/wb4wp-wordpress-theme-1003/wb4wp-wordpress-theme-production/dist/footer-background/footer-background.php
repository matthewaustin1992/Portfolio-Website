<?php // @codingStandardsIgnoreStart

use Wb4WpTheme\Managers\Customize\Customize_Settings;
use Wb4WpTheme\Helpers\Template_Helper;

$video_background = Customize_Settings::get_setting( 'wb4wp_footer_section_background_video_setting' );
?>

<div class="kv-background <?= isset($video_background) ? 'kv-video' : '' ?>" style="background-color: var(--wb4wp-footer-background, transparent)">
	<?php
		$background_style = array(
			'background-size' => !empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_effect_setting' )) && Customize_Settings::get_setting( 'wb4wp_footer_section_background_effect_setting' ) === 'contain' ? Customize_Settings::get_setting( 'wb4wp_footer_section_background_effect_setting' ) : null,
			'background-attachment' => !empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_effect_setting' )) && Customize_Settings::get_setting( 'wb4wp_footer_section_background_effect_setting' ) === 'fixed' ? Customize_Settings::get_setting( 'wb4wp_footer_section_background_effect_setting' ) : null,
			'background-color' => 'var(--wb4wp-footer-background)',
			'background-image' => !empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_image_setting' )) ? 'url('.Customize_Settings::get_setting( 'wb4wp_footer_section_background_image_setting' ).')' : null,
			'opacity' => (int) (!empty(Customize_Settings::get_setting( 'wb4wp_footer_section_background_opacity_setting' )) ? Customize_Settings::get_setting( 'wb4wp_footer_section_background_opacity_setting' ) : '100') / 100,
		)
	?>
	<div 
		class="kv-background-inner pattern-black-<?= Customize_Settings::get_setting( 'wb4wp_footer_section_background_pattern_index_setting' ) ?>"
		style="<?= Template_Helper::array_to_inline_css($background_style) ?>"
	>	
		<?php if(!empty($video_background)):?>
		<video autoplay="true" loop="loop" muted="muted" playsinline="" preload="auto">
			<source 
				type="video/mp4" 
				data-src="<?= $video_background ?>" 
				src="<?= $video_background ?>"
			>
		</video>
		<?php endif; ?>
	</div>
</div>