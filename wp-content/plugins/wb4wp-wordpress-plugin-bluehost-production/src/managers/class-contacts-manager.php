<?php

namespace Wb4Wp\Managers;

use WP_REST_Response;

/**
 * Class Contacts_Manager
 * @package Wb4Wp\Managers
 */
final class Contacts_Manager {

	private $table_name = 'wb4wp_contacts';

	/**
	 * Inserts or updates a contact by email address
	 *
	 * @param $request
	 *
	 * @return WP_REST_Response
	 */
	public function contacts_upsert( $request ) {
		global $wpdb;

		$json = $request->get_json_params();

		$validated_fields = $this->validate_contacts_request( $json );
		if ( ! is_array( $validated_fields ) ) {
			return new WP_REST_Response( $validated_fields, 400 );
		}

		$table_name = $wpdb->prefix . $this->table_name;

		$exists = $wpdb->get_results(
			$wpdb->prepare( 'SELECT contact_id FROM %s WHERE email_address=%s', $table_name, $validated_fields['email_address'] )
		);

		if ( ! empty( $exists ) ) {
			$sql_result = $wpdb->update(
				$table_name,
				array_merge(
					$validated_fields,
					array(
						//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
						'modified_on' => date( 'Y-m-d H:i:s' ),
					)
				),
				array(
					'contact_id' => $exists[0]->contact_id,
				)
			);
			$contact_id = $exists[0]->contact_id;
		} else {
			$sql_result  = $wpdb->insert(
				$table_name,
				$validated_fields
			);
			$last_insert = $wpdb->get_row(
				$wpdb->prepare( 'SELECT contact_id from %s ORDER BY contact_id DESC', $table_name )
			);
			$contact_id  = ! empty( $last_insert ) ? $last_insert->contact_id : 0;
		}

		if ( false === $sql_result ) {
			return new WP_REST_Response(
				array(
					'success' => false,
				),
				500
			);
		}

		do_action( 'wb4wp_contacts_updated', $contact_id );

		return new WP_REST_Response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * Validates the request data.
	 * If a field in the request does not exist
	 * in the database, that field will be removed
	 * from the request data and thus will not be
	 * inserted.
	 *
	 * @param array $request_fields
	 *
	 * @return string|array
	 */
	private function validate_contacts_request( array &$request_fields ) {
		global $wpdb;

		$table_name       = $wpdb->prefix . $this->table_name;
		$query            = $wpdb->prepare( 'DESCRIBE %s', $table_name );
		$existing_columns = $query->get_results( $query );

		$required_fields = array_column(
			array_filter(
				$existing_columns,
				function ( $v ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					return 'YES' !== $v->Null && '' === $v->Extra && null === $v->Default;
				},
				ARRAY_FILTER_USE_BOTH
			),
			'Field'
		);

		$fields_diff = array_diff( $required_fields, array_keys( $request_fields ) );
		if ( ! empty( $fields_diff ) ) {
			return 'Missing required field(s): ' . implode( ', ', $fields_diff );
		}

		foreach ( $request_fields as $field_name => $field_value ) {
			if ( ! in_array( $field_name, array_column( $existing_columns, 'Field' ), true ) ) {
				unset( $request_fields[ $field_name ] );
				continue;
			}

			if ( in_array( $field_name, $required_fields, true ) && empty( rtrim( $field_value ) ) ) {
				return 'Field "' . $field_name . '" cannot be empty';
			}
		}

		return $request_fields;
	}

}
