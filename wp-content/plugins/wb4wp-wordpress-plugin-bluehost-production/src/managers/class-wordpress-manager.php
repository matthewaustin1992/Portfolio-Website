<?php

namespace Wb4Wp\Managers;

use DateTime;
use Exception;
use Wb4Wp\Helpers\Creative_Mail_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Helpers\WP_Post_Revisioning_Helper;
use WP_Post;
use WP_REST_Response;

/**
 * Class WordPress_Manager
 * @package Wb4Wp\Managers
 */
final class WordPress_Manager {

	/**
	 * Removes all current pages in the WordPress instance.
	 */
	public static function reset_wp_instance() {
		$pages = get_pages(
			array(
				'post_status' => array(
					'publish',
					'pending',
					'draft',
					'auto-draft',
					'future',
					'private',
					'inherit',
				),
			)
		);

		foreach ( $pages as $page ) {
			wp_delete_post( $page->ID );
		}
	}

	/**
	 * Stores a post object as auto-save
	 *
	 * @param array $post_data
	 *
	 * @return bool
	 */
	public static function store_auto_save( array $post_data ) {
		$the_future                     = new DateTime( '+20 seconds' );
		$post_data                      = self::wp_get_allowed_post_data( $post_data );
		$post                           = (array) get_post( $post_data['ID'] );
		$post_data                      = array_merge( $post, $post_data );
		$post_data['post_modified']     = $the_future->format( 'Y-m-d H:i:s' );
		$post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $the_future->getTimestamp() );
		$old_auto_save                  = wp_get_post_autosave( $post_data['ID'] );

		if ( $old_auto_save ) {
			$new_auto_save       = _wp_post_revision_data( $post_data, true );
			$new_auto_save['ID'] = $old_auto_save->ID;

			// If the new auto-save has the same content as the post, delete the auto-save.
			$auto_save_is_different = false;
			foreach ( array_intersect( array_keys( $new_auto_save ), array_keys( _wp_post_revision_fields( $post ) ) ) as $field ) {
				if ( normalize_whitespace( $new_auto_save[ $field ] ) !== normalize_whitespace( $post[ $field ] ) ) {
					$auto_save_is_different = true;
					break;
				}
			}


			// This code seems bugged? ... is this really tiggered ?
			if ( ! $auto_save_is_different ) {
				delete_post_meta( $old_auto_save->ID, 'WB4WP_DRAFT' );
				wp_delete_post_revision( $old_auto_save->ID );
				return true;
			}

			do_action( 'wp_creating_autosave', $new_auto_save );
			return wp_update_post( $new_auto_save );
		}

		// _wp_put_post_revision() expects unescaped.
		$post_data = wp_unslash( $post_data );

		// Otherwise create the new auto-save as a special post revision.
		$revision_id = _wp_put_post_revision( $post_data, true );
		if ( $revision_id ) {
			update_post_meta( $revision_id, 'WB4WP_DRAFT', true );
			return $revision_id;
		}

		return $revision_id;
	}

	/**
	 * Copied from /wp-admin/includes/post.php
	 *
	 * @param null $post_data
	 *
	 * @return array|null
	 */
	private static function wp_get_allowed_post_data( $post_data = null ) {
		if ( empty( $post_data ) ) {
			// phpcs:ignore
			$post_data = $_POST;
		}

		// Pass through errors.
		if ( is_wp_error( $post_data ) ) {
			return $post_data;
		}

		return array_diff_key( $post_data, array_flip( array( 'meta_input', 'file', 'guid' ) ) );
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'apply_public_window_object' ) );
	}

	public function apply_public_window_object() {
		wp_register_script( 'window-public-object', '', false, WB4WP_PLUGIN_VERSION, false );
		wp_enqueue_script( 'window-public-object' );

		add_inline_js_data_object(
			'window-public-object',
			array(
				'root'                  => site_url( '/' ),
				'creativeMailInstalled' => Creative_Mail_Helper::is_creative_mail_installed(),
				'pluginVersion'         => WB4WP_PLUGIN_VERSION,
				'themeVersion'          => Theme_Manager::get_current_theme_version(),
			)
		);
	}

	public function get_draft_pages() {
		try {
			$template     = 'wb4wp-template.php';
			$drafts       = get_pages(
				array(
					'post_status' => array( 'draft' ),
					'meta_key'    => '_wp_page_template',
					'meta_value'  => $template,
				)
			);
			$ee_page_ids  = array_column(
				get_pages(
					array(
						'post_type'  => 'page',
						'meta_query' => array(
							'template_clause' => array(
								'key'     => '_wp_page_template',
								'value'   => $template,
								'compare' => '=',
							),
							'wb4wp_clause'    => array(
								'key'     => 'WB4WP_EDIT_MODE',
								'compare' => 'EXISTS',
							),
						),
					)
				),
				'ID'
			);
			$ee_revisions = array();

			foreach ( $ee_page_ids as $ee_page_id ) {
				$revision = $this->get_latest_revision( $ee_page_id );
				if ( ! empty( $revision ) ) {
					$ee_revisions[] = $revision;
				}
			}

			return new WP_REST_Response( array_merge( $ee_revisions, $drafts ), 200 );
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	/**
	 * Get latest revision for a post
	 *
	 * @param $post_id
	 *
	 * @return int|WP_Post
	 */
	private function get_latest_revision( $post_id ) {
		$posts = wp_get_post_revisions( $post_id );

		usort(
			$posts,
			function ( $a, $b ) {
				return strtotime( $b->post_modified_gmt ) - strtotime( $a->post_modified_gmt );
			}
		);
		$latest = reset( $posts );

		return ( ! empty( $latest ) && ! empty( get_post_meta( $latest->post_parent, 'WB4WP_DRAFT', true ) ) ) ? $latest : null;
	}

	public function publish_pages( $request ) {
		try {
			foreach ( $request->get_json_params() as $page_id ) {
				$post = get_post( $page_id );

				if ( ! empty( $post ) ) {
					if ( 'revision' === $post->post_type ) {
						$this->restore_post( $post );

						delete_post_meta( $post->post_parent, 'WB4WP_DRAFT' );
						$older_revisions = wp_get_post_revisions( $post->post_parent );

					
						// After we restore the latest revision, we delete all the old ones.
						foreach ( $older_revisions as $revision ) {
							wp_delete_post_revision( $revision->ID );

							WP_Post_Revisioning_Helper::delete_old_revision_asset($revision->ID);

							delete_post_meta( $revision->post_parent, 'WB4WP_DRAFT' );
						}
					} else {
						wp_publish_post( $page_id );
					}
				}
			}

			Theme_Manager::install_theme();

			if ( Provider_Helper::is_bluehost() ) {
				update_option( 'mm_coming_soon', 'false' );
			}

			return new WP_REST_Response( null, 204 );
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	public function restore_post( $post ) {
		$new_post               = get_post( $post->post_parent );
		$new_post->post_content = $post->post_content;

		$new_post->meta_input = array(
			'WB4WP_EDIT_MODE'    => 'builder',
			'_wp_page_template'  => 'wb4wp-template.php'
		);

		WP_Post_Revisioning_Helper::wp_restore_post_revision_meta($new_post->ID, $post->ID);

		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
		remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

		$response = wp_insert_post( $new_post );

		add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		add_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

		return $response;
	}

	/**
	 * Change the current theme and disable the current plugin
	 *
	 * @param $request
	 */
	public function disable_plugin( $request ) {
		$current_theme = wp_get_theme();

		if ( strpos( $current_theme->stylesheet, 'wb4wp' ) !== false ) {
			$themes = wp_get_themes();

			if ( ! empty( $request['theme'] ) && wp_get_theme( $request['theme'] )->exists() ) {
				switch_theme( $request['theme'] );
			} else {
				foreach ( $themes as $theme ) {
					if ( strpos( $theme->stylesheet, 'wb4wp' ) === false ) {
						switch_theme( $theme->stylesheet );
						break;
					}
				}
			}
		}

		deactivate_plugins( WB4WP_PLUGIN_FILE );
	}
}
