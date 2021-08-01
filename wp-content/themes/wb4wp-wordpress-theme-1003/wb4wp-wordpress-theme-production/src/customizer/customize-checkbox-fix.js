jQuery( document ).ready(
	function () {
		const checkboxes = document.querySelectorAll( 'input[value]' );
		checkboxes.forEach(
			function ( checkbox ) {
				checkbox.checked = checkbox.value === 'true';
			}
		);
	}
);
