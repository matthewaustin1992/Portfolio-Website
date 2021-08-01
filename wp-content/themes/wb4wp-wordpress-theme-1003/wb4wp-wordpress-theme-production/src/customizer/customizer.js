/**
 * File to customize the customizer
 */

jQuery( document ).ready(
	function () {
		const wrapper = document.createElement( 'li' );
		wrapper.id    = 'accordion-section-wb4wp';
		wrapper.setAttribute( 'aria-owns', 'accordion-section-wb4wp' );
		wrapper.classList.add(
			'accordion-section',
			'control-section',
			'control-section-default',
		);

		const link = document.createElement( 'a' );
		link.href  = wb4wpL10n.home_url + '/wp-admin/admin.php?page=wb4wp-editor';

		const title     = document.createElement( 'h3' );
		title.className = 'accordion-section-title';
		title.innerText = wb4wpL10n.title_text;

		link.appendChild( title );
		wrapper.appendChild( link );

		document.getElementById( 'accordion-section-publish_settings' ).after( wrapper );
	}
);
