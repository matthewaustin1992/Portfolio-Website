<?php // @codingStandardsIgnoreStart

use Wb4Wp\Managers\Template_Manager;

get_header();

$post = get_post();

if ( ! empty( $post ) ) {
	Template_Manager::output_essential_scripts_and_json_objects_used_by_express_editor_runtime( $post->ID );
}
?>

	<link href="https://components.mywebsitebuilder.com/fonts/font-awesome.css" rel="stylesheet">

	<div class="site kv-site kv-main" tabindex="-1">
		<a href="#ada-tab-anchor" class="skip-link" data-tag="ada-skip-link"></a>
		<div class="kv-control-placeholder kv-_site" data-kv-control="0">
		</div>
		<content class="kv-page" name="page-home">
			<div class="kv-page-content">
				<?php echo apply_filters( 'wb4wp_section_output', $post->post_content ) ?>
			</div>
		</content>
	</div>
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="pswp__bg"></div>
		<div class="pswp__scroll-wrap">
			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>
			<div class="pswp__ui pswp__ui--hidden">
				<div class="pswp__top-bar">
					<div class="pswp__counter"></div>
					<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
					<button class="pswp__button pswp__button--share" title="Share"></button>
					<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
					<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
					<div class="pswp__preloader">
						<div class="pswp__preloader__icn">
							<div class="pswp__preloader__cut">
								<div class="pswp__preloader__donut"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
					<div class="pswp__share-tooltip"></div>
				</div>
				<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
				<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
				<div class="pswp__caption" style="display: none;">
					<div class="pswp__caption__center"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- #page -->

<?php get_footer(); ?>
