<?php

/**
 * Unlink functionality for environments except production
 */

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    // phpcs:ignore
	if ( 'disconnect' === $_POST['action'] ) {
		Wb4Wp\Helpers\Options_Helper::unlink();

		?>

		<div class="eewp-kvp">
			<p>Your WordPress instance is now unlinked from your Bluehost account. Please click the button below to
				redirect back to your WordPress environment.</p>
		</div>

		<div class="eewp-kvp">
			<a href="/wp-admin/" class="eewp-button-text-primary">Back to WordPress</a>
		</div>

		<?php
	}
} else {

	?>

	<div class="eewp-kvp">
		<p>Your WordPress instance is connected to your Bluehost account. If you would like to unlink your WordPress
			instance from your account, please click the 'Unlink' button below. <strong>Unlinking your account is
				permanent and cannot be undone.</strong></p>
	</div>

	<div class="eewp-kvp">
		<form name="disconnect" method="post">
			<input type="hidden" name="action" value="disconnect"/>
			<input name="disconnect_button" type="submit" class="eewp-button-text-primary destructive"
				id="disconnect-instance" value="Unlink"
				onclick="return confirm('Are you sure you want to unlink your Bluehost account from your WordPress site? This action is permanent and cannot be undone.')"/>
		</form>
	</div>

<?php } ?>
