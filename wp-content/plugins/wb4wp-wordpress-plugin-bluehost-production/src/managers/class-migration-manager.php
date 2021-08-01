<?php

namespace Wb4Wp\Managers;

use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Provider_Helper;

/**
 * Class Migration_Manager
 * @package Wb4Wp\Managers
 */
final class Migration_Manager {

	public function __construct() {
		add_action( 'init', array( $this, 'migrate' ) );
	}

	public function migrate() {
		$migrations = array(
			'_migrate_22_01_2021',
			'_migrate_23_02_2021',
			'_migrate_29_03_2021',
		);

		$previous_migrations = json_decode( Options_Helper::get( WB4WP_MIGRATIONS, '[]' ) );
		foreach ( $migrations as $migration ) {
			if ( method_exists( $this, $migration ) && ! in_array( $migration, $previous_migrations, true ) ) {
				$migration_function = str_replace( '_migrate', 'migrate', $migration );
				$this->$migration_function();
				$previous_migrations[] = $migration;
			}
		}

		// phpcs:ignore
		Options_Helper::set( WB4WP_MIGRATIONS, json_encode( $previous_migrations ) );
	}

	private function migrate_22_01_2021() {
		if ( $this->get_current_plugin_version() > 1560 ) {
			$wb4wp_menu = wp_get_nav_menu_object( Provider_Helper::get_provider_name() );

			if ( ! empty( $wb4wp_menu ) ) {
				$locations          = get_theme_mod( 'nav_menu_locations' );
				$locations['wb4wp'] = $wb4wp_menu->term_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}
	}

	private function get_current_plugin_version() {
		$plugin_data = get_file_data( WB4WP_PLUGIN_FILE, array( 'Version' => 'Version' ), false );
		if ( empty( $plugin_data ) ) {
			return null;
		}

		$plugin_version = strstr( $plugin_data['Version'], ' ', true );
		$pos            = strpos( $plugin_version, '-' );
		if ( false === $pos ) {
			return null;
		}

		$current_build = substr( $plugin_version, $pos + 1 );
		if ( empty( $current_build ) ) {
			return null;
		}

		return (int) $current_build;
	}

	private function migrate_23_02_2021() {
		if ( $this->get_current_plugin_version() > 1620 ) {
			global $wpdb;

			$table_name      = $wpdb->prefix . 'wb4wp_contacts';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
                contact_id mediumint(9) NOT NULL AUTO_INCREMENT,
                first_name varchar(150) DEFAULT NULL,
                last_name varchar(200) DEFAULT NULL,
                email_address varchar(254) UNIQUE NOT NULL,
                prefix varchar(10) DEFAULT NULL,
                phone varchar(50) DEFAULT NULL,
                company_name varchar(150) DEFAULT NULL,
                job_title varchar(150) DEFAULT NULL,
                birthday datetime DEFAULT NULL,
                source varchar(50) DEFAULT NULL,
                opt_in datetime DEFAULT NULL,
                opt_out datetime DEFAULT NULL,
                created_on datetime DEFAULT NOW(),
                modified_on datetime DEFAULT NULL,
                uuid varchar(100) DEFAULT NULL,
                PRIMARY KEY (contact_id)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	private function migrate_29_03_2021() {
		if ( $this->get_current_plugin_version() > 1875 ) {
			global $wpdb;

			// phpcs:ignore
			$results = $wpdb->get_results(
				"SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE 'theme_mods_wb4wp-%'"
			);

			foreach ( $results as $result ) {
				if ( 'theme_mods_' . get_option( 'stylesheet' ) !== $result->option_name ) {
					delete_option( $result->option_name );
				}
			}
		}
	}

}
