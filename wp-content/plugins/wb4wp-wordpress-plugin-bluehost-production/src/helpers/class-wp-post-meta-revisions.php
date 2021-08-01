<?php
 
 namespace Wb4Wp\Helpers;
 use Wb4Wp\Managers\Raygun_Manager;
 use Exception;

/**
 * Class WP_Post_Revisioning_Helper.
 */
final class WP_Post_Revisioning_Helper {

	/**
	 * Set up the plugin actions.
	 */
	public function __construct() {

		// Actions.
		//
		// When restoring a revision, also restore that revisions's assets file.
		add_action( 'wp_restore_post_revision', array( $this, 'wp_restore_post_revision_meta' ), 10, 2 );
	}

	/**
	 * Restore the revisioned meta values for a post.
	 *
	 * @param int $post_id     The ID of the post to restore the meta to.
	 * @param int $revision_id The ID of the revision to restore the meta from.
	 *
	 * @since 1.0.0
	 */
	public static function wp_restore_post_revision_meta( $post_id, $revision_id ) {

		$asset_file_revision = ABSPATH . 'wp-content/uploads/wb4wp-page-assets/assets_' . $revision_id . '.json';
		$asset_file_new_post = ABSPATH . 'wp-content/uploads/wb4wp-page-assets/assets_' . $post_id . '.json';
		if ( file_exists( $asset_file_revision ) ) {
			file_put_contents( $asset_file_new_post, file_get_contents( $asset_file_revision ) );
		}		
	}

	public static function delete_old_revision_asset( $revision_id ) {
		$asset_file_revision = ABSPATH . 'wp-content/uploads/wb4wp-page-assets/assets_' . $revision_id . '.json';

		if ( file_exists( $asset_file_revision ) ) {
			$file_is_deleted = unlink($asset_file_revision);

			if ( ! $file_is_deleted ) {
				Raygun_Manager::get_instance()->exception_handler( new Exception( "Couldn't delete {$asset_file_revision}" ) );
			}
		}	
	}
}