const Selectors = {
	BlogPagesPanelBackButton:
		'ul#sub-accordion-panel-wb4wp_blog_pages_panel .customize-panel-back',
	SinglePostSectionButton: 'li#accordion-section-wb4wp_single_post_section',
	BlogOverviewSectionButton: 'li#accordion-section-wb4wp_blog_overview_section',
};

function getPreviewIFrame() {
	return document.querySelector( '#customize-preview iframe' );
}

function updatePreviewUrl( url ) {
	const iframe = getPreviewIFrame();

	const currentUrl  = new URL( iframe.getAttribute( 'src' ) );
	const previewUrl  = new URL( url );
	previewUrl.search = currentUrl.search;

	if ( currentUrl.toString() === previewUrl.toString() ) {
		return;
	}

	iframe.setAttribute( 'src', previewUrl.toString() );
}

jQuery( document ).ready(
	function () {
		const previewPageConfig = {
			[Selectors.BlogPagesPanelBackButton]: wb4wpUrls.base,
			[Selectors.SinglePostSectionButton]: wb4wpUrls.singlePost,
			[Selectors.BlogOverviewSectionButton]: wb4wpUrls.blogOverview,
		};

		Object.entries( previewPageConfig ).forEach(
			function ( [selector, previewUrl] ) {
				document
				.querySelector( selector )
				.addEventListener(
					'click',
					function () {
						updatePreviewUrl( previewUrl );
					}
				);
			}
		);
	}
);
