<?php

namespace Wb4Wp\Managers;

use WP_REST_Response;

/**
 * Class Autosave_Manager
 * @package Wb4Wp\Managers
 */
class Autosave_Manager {

	public static function handle_publish_autosave( $request ) {
		if ( empty( $request['post_id'] ) ) {
			return self::error_response( 'No post id.', 400 );
		}

		$post_id  = (int) $request['post_id'];
		$autosave = wp_get_post_autosave( $post_id );

		if ( empty( $autosave ) ) {
			return self::error_response( "No autosave found for post with id '{$post_id}'." );
		}

		wp_restore_post_revision( $autosave->ID );

		$post_type_object = get_post_type_object( get_post( $post_id )->post_type );
		$edit_post_url    = apply_filters(
			'get_edit_post_link',
			admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $post_id ) ),
			$post_id,
			'url'
		);

		$redirect_url = add_query_arg(
			array(
				'message'  => 5,
				'revision' => $autosave->ID,
			),
			$edit_post_url
		);

		return self::success_response( array( 'redirect_url' => $redirect_url ) );
	}

	public static function handle_discard_autosave( $request ) {
		if ( empty( $request['post_id'] ) ) {
			return self::error_response( 'No post id.', 400 );
		}

		$post_id  = (int) $request['post_id'];
		$autosave = wp_get_post_autosave( $post_id );

		if ( empty( $autosave ) ) {
			return self::error_response( "No autosave found for post with id '{$post_id}'." );
		}

		wp_delete_post_revision( $autosave->ID );

		return self::success_response();
	}

	private static function error_response( $message, $status = 500 ) {
		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => $message,
			),
			$status
		);
	}

	/**
	 * @param array|null $data
	 *
	 * @return WP_REST_Response
	 */
	private static function success_response( $data = null ) {
		$response_data = array( 'success' => true );

		if ( ! empty( $data ) ) {
			$response_data = array_merge( $data, $response_data );
		}

		return new WP_REST_Response( $response_data, 200 );
	}

}
