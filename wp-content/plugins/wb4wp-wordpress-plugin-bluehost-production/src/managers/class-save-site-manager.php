<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Helpers\Performance_Helper;
use Wb4Wp\Helpers\Post_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Helpers\Site_Model_Helper;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Save_Site_Manager
 * @package Wb4Wp\Managers
 */
final class Save_Site_Manager {

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public static function handle_save_site( $request ) {
		global $wpdb;
		$performance_helper = new Performance_Helper();
		$performance_helper->start();

		// In this endpoint we will convert the Express Editor site model to the WordPress data model.
		// It will sync all settings for your website, its optimized for the # of queries excuted on the database.
		// Steps:
		// 1. Save site meta data.
		// 2. update the theme options
		// 3. save the pages.
		// 4. update the navigation.
		// 5. store request on disk. so that we can use it for the "get" description.
		// 6. set plugin state to onboarded.

		try {
			$json      = $request->get_json_params();
			$author_id = Request_Manager::get_logged_in_user_id( $request );

			$site_description = $json['site_description'];

			$pages = $site_description['pages'];
			$performance_helper->get_delta_time( 'read_input' );

			$bulk_update_wp_options_table = array();

			// 1. Save site meta data.
			$bulk_update_wp_options_table = self::save_site_meta( $bulk_update_wp_options_table, $site_description, $json['site_name'] );
			$performance_helper->get_delta_time( 'save_site_meta' );

			// 2. update the theme options.
			$bulk_update_wp_options_table = self::save_theme_options( $bulk_update_wp_options_table, $site_description, $json['published_pages'] );
			$performance_helper->get_delta_time( 'save_theme_options' );

			// 3. save the pages.
			$result                       = self::save_pages( $bulk_update_wp_options_table, $author_id, $pages, $json['published_pages'] );
			$bulk_update_wp_options_table = $result['bulk_update_wp_options_table'];
			$saved_pages                  = $result['saved_pages'];

			$performance_helper->get_delta_time( 'save_pages' );

			// 4. update the navigation.
			self::save_navigation( $site_description['navigation'], $pages, $saved_pages );
			$performance_helper->get_delta_time( 'save_navigation' );

			// 5. store request on disk. so that we can use it for the "get" description.

			// 6. set plugin state to onboarded
			$bulk_update_wp_options_table[ WB4WP_PLUGIN_STATE ] = 'onboarded';

			// TODO this is used for the description get call. It should be replace by a "site_model" json in the next release.
			// we dont want this in the wp options anymore.
			$bulk_update_wp_options_table[ WB4WP_GLOBAL_SECTIONS ]      = $site_description['globalSections'];
			$bulk_update_wp_options_table[ WB4WP_THEME ]                = $site_description['theme'];
			$bulk_update_wp_options_table[ WB4WP_GLOBAL_BINDING ]       = $site_description['globalBinding'];
			$bulk_update_wp_options_table[ WB4WP_METADATA ]             = $site_description['metadata'];
			$bulk_update_wp_options_table[ WB4WP_API_KEYS ]             = $site_description['apiKeys'];
			$bulk_update_wp_options_table[ WB4WP_URLS ]                 = $site_description['urls'];
			$bulk_update_wp_options_table[ WB4WP_FEATURE_STORAGE ]      = $site_description['featureStorage'];
			$bulk_update_wp_options_table[ WB4WP_GLOBAL_FEATURE_MODEL ] = $site_description['globalFeatureModel'];

			//phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$rows_affected = $wpdb->query( self::convert_bulk_update_wp_options_table_to_sql( $bulk_update_wp_options_table ) );
			$performance_helper->get_delta_time( 'save_wp_option_keys' );

			if ( false === $rows_affected ) {
				Raygun_Manager::get_instance()->exception_handler( $wpdb->print_error() );
			}

			$performance = $performance_helper->finish();

			// Log stats to raygun if it took longer then 15 sec.
			if ( $performance['total'] > 15 ) {
				Raygun_Manager::get_instance()->exception_handler( new Exception( wp_json_encode( $performance ) ) );
			}

			return new WP_REST_Response(
				array(
					'success'           => true,
					'performance'       => $performance,
					'number_of_queries' => $wpdb->num_queries,
					'queries'           => $wpdb->queries,

				),
				200
			);
		} catch ( Exception $exception ) {
			$performance = $performance_helper->finish();

			return new WP_REST_Response(
				array(
					'success'     => false,
					'message'     => $exception->getMessage(),
					'performance' => $performance,
				),
				200
			);
		}
	}

	private static function convert_bulk_update_wp_options_table_to_sql( $bulk_update_wp_options_table ) {
		global $wpdb;
		// TODO Move this to the options helper. This technique / query is way faster.
		$sql_query = "INSERT INTO {$wpdb->prefix}options" . ' (option_name, option_value)
				VALUES';

		if ( count( $bulk_update_wp_options_table ) > 0 ) {
			foreach ( $bulk_update_wp_options_table as $key => $value ) {

				$str_value = $value;
				if ( is_array( $value ) ) {
					// phpcs:disable WordPress.WP.AlternativeFunctions.json_encode_json_encode
					$str_value = json_encode( $value );
				}

				$str_value = str_replace( "'", "''", $str_value );

				$sql_query = $sql_query . "('" . $key . "', '" . $str_value . "'),";
			}

			// remove last ",".
			$sql_query = rtrim( $sql_query, ',' );
		}

		$sql_query = $sql_query . 'ON DUPLICATE KEY UPDATE
			option_name=VALUES(option_name), option_value=VALUES(option_value)';

		return $sql_query;
	}

	private static function save_pages( $bulk_update_wp_options_table, $author_id, $pages, $published_pages ) {
		$saved_pages = array();

		// Save pages steps
		// a. get a mapping of the pages that previously existed.
		// b. save every page seperately.
		// c. remove the deleted pages.
		// d. store the base style in the assets folder.

		// a. get a mapping of the pages that previously existed.
		$express_editor_page_id_to_wordpress_page_id_mapping = self::get_express_editor_page_id_to_wp_page_id_mapping();

		// b. save every page seperately.
		// For each page in the express editor model, update the last draft or create a new page.
		foreach ( $pages as $page ) {
			// find the published page.

			$published_page = current(
				array_filter(
					$published_pages['pages'],
					function ( $published_page ) use ( $page ) {
						return isset( $published_page['pageId'] ) && $published_page['pageId'] === $page['id'];
					}
				)
			);

			// no published page record found. we have nothing to update.
			if ( empty( $published_page ) || empty( $published_page['sections'] ) ) {
				continue;
			}

			// All information that we need to update a page.
			$new_page = self::save_page( $author_id, $page, $published_page, $express_editor_page_id_to_wordpress_page_id_mapping );

			array_push( $saved_pages, $new_page );

			// RJ Todo #1.
			if ( true === $page['mainPage'] ) {
				$bulk_update_wp_options_table['page_on_front']        = $new_page['wp_id'];
				$bulk_update_wp_options_table['show_on_front']        = 'page';
				$bulk_update_wp_options_table[ WB4WP_HEADER_SECTION ] = $page['headerSection'];
			}
		}

		// c. remove the deleted pages.
		foreach ( $express_editor_page_id_to_wordpress_page_id_mapping as $express_editor_page_currently_in_db ) {
			// we must only delete pages that are created by the Express Editor.
			if ( true !== $express_editor_page_currently_in_db['can_be_deleted'] ) {
				continue;
			}

			// check if the pages is saved.
			$page_is_saved = current(
				array_filter(
					$saved_pages,
					function ( $saved_page ) use ( $express_editor_page_currently_in_db ) {
						return isset( $saved_page['ee_id'] ) && strval( $saved_page['ee_id'] ) === strval( $express_editor_page_currently_in_db['ee_id'] );
					}
				)
			);

			if ( empty( $page_is_saved ) ) {
				// page is not saved, so it must be deleted.?
				wp_delete_post( $express_editor_page_currently_in_db['wp_id'] );
			}
		}

		return array(
			'saved_pages'                  => $saved_pages,
			'bulk_update_wp_options_table' => $bulk_update_wp_options_table,
		);
	}

	private static function save_page( $author_id, $page, $published_page, $express_editor_page_id_to_wordpress_page_id_mapping ) {
		$page_id = $page['id'];
		// Steps to save a page
		// a. temp disable the secure content filter (allowing us to inject custom html code in a post (like style attributes))
		// b. get the published (html, css, javascript) data for each section
		// c. convert published section data to page content using gutenberg blocks.
		// d. determin of we need to update an existing page or create a new one, and update the page.
		// e. store the base style in the assets folder
		// f. enable again: temp disable the secure content filter (allowing us to inject custom html code in a post (like style attributes)).

		// a. temp disable the secure content filter (allowing us to inject custom html code in a post (like style attributes)).
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
		remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

		// b. get the published (html, css, javascript) data for each section.
		$one_page_published_section_data = self::get_published_section_data_map_from_one_page( $page, $published_page );

		// c. convert published section data to page content using gutenberg blocks.
		$post_content = Post_Helper::generate_post_content( $page, $one_page_published_section_data );

		// d. determin of we need to update an existing page or create a new one, and update the page.
		// check if the page exists before.
		$post_id_to_store_assets = null;
		if ( array_key_exists( $page_id, $express_editor_page_id_to_wordpress_page_id_mapping ) ) {
			$wp_id       = $express_editor_page_id_to_wordpress_page_id_mapping[ $page_id ]['wp_id'];
			$post_status = $express_editor_page_id_to_wordpress_page_id_mapping[ $page_id ]['post_status'];

			// the page is an existing page, lets update the page.
			$post_id_to_store_assets = self::save_existing_page( $page, $published_page, $post_content, $wp_id, $post_status );
		} else {
			// the page is a new page. lets create a new one.
			$wp_id                   = self::save_new_page( $page, $published_page, $post_content, $author_id );
			$post_id_to_store_assets = $wp_id;
		}

		// e. store the base style in the assets folder.
		$uploads_path = ABSPATH . 'wp-content/uploads/wb4wp-page-assets/';

		if ( ! file_exists( $uploads_path ) ) {
			mkdir( $uploads_path, 0777, true );
		}

		$assets = array(
			'baseStyle'     => $published_page['baseStyle'],
			'featureScript' => $published_page['featureScript'],
			'siteModel'     => $published_page['siteModel'],
		);

		if ( ! empty( $published_pages['theme']['fonts'] ) ) {
				$assets['fonts'] = $published_pages['theme']['fonts'];
		}

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		file_put_contents( $uploads_path . 'assets_' . $post_id_to_store_assets . '.json', wp_json_encode( $assets ) );
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents

		// f. enable again: temp disable the secure content filter (allowing us to inject custom html code in a post (like style attributes)).
		add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		add_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

		// This array will be used to remove deleted pages from your WordPress instance.
		return array(
			'ee_id' => $page_id,
			'wp_id' => $wp_id,
		);
	}

	/**
	 * @param array $ee_page
	 * @param array $published_pages
	 *
	 * @return array
	 */
	private static function get_published_section_data_map_from_one_page( $page, $published_page ) {
		$published_sections = $published_page['sections'];

		// Remove a few section that we don't need in the WordPress Page (these sections are handled by the theme).
		array_shift( $published_sections ); // Remove header section.
		array_pop( $published_sections ); // Remove footer section.

		$published_section_data_map = array();
		foreach ( $published_sections as $section_data ) {
			if ( empty( $section_data['id'] ) ) {
				continue;
			}

			$published_section_data_map[ $section_data['id'] ] = $section_data;
		}

		return $published_section_data_map;
	}

	private static function save_existing_page( $page, $published_page, $post_content, $wp_id, $post_status ) {
		$page_update_arguments = array(
			'ID'           => $wp_id,
			'post_title'   => $page['title'],
			'post_name'    => $page['uriPath'],
			'post_content' => $post_content,
			'meta_input'   => array(
				'WB4WP_EDIT_MODE'               => 'builder',
				'_wp_page_template'             => 'wb4wp-template.php',
				'WB4WP_PAGE_ID'                 => $page['id'],
				'WB4WP_PAGE_TYPE'               => isset( $page['pageType'] ) ? $page['pageType'] : '',
				'WB4WP_PAGE_MATCH_URI_TO_TITLE' => $page['matchUriToTitle'],
				'wb4wp_site_model'              => $published_page['siteModel'],
				'wb4wp_feature_script'          => $published_page['featureScript'],
			),
		);

		// We might have to create an auto save.
		if ( 'publish' === $post_status ) {
			return WordPress_Manager::store_auto_save( $page_update_arguments );
		}

		// just update the existing draft.
		return wp_update_post( $page_update_arguments );
	}

	/**
	 * @param array $ee_page
	 * @param string $author_id
	 * @param string $post_content
	 *
	 * @return string
	 */
	private static function save_new_page( $page, $published_page, $post_content, $author_id ) {
		return (string) wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_author'  => $author_id,
				'post_content' => $post_content,
				'post_name'    => $page['uriPath'],
				'post_status'  => 'draft',
				'post_type'    => 'page',
				'meta_input'   => array(
					'WB4WP_EDIT_MODE'               => 'builder',
					'_wp_page_template'             => 'wb4wp-template.php',
					'WB4WP_PAGE_ID'                 => $page['id'],
					'WB4WP_PAGE_MATCH_URI_TO_TITLE' => $page['matchUriToTitle'],
					'wb4wp_site_model'              => $published_page['siteModel'],
					'wb4wp_feature_script'          => $published_page['featureScript'],
				),
			)
		);
	}

	/**
	 * Returns the pages mapping
	 *
	 * @return array
	 */
	public static function get_express_editor_page_id_to_wp_page_id_mapping() {
		global $wpdb;

		// Get all pages and meta data that is relevant to us.
		$results = $wpdb->get_results(
			//phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			$wpdb->prepare( "SELECT *, (select meta_value from `{$wpdb->prefix}postmeta` where meta_key = 'WB4WP_PAGE_ID' and post_id = post.id) as ee_page_id FROM `{$wpdb->prefix}posts` as post WHERE post_status in ('draft', 'publish') and post_type = 'page'", array() )
		);

		$express_editor_page_id_to_wordpress_page_id_mapping = array();

		foreach ( $results as $db_page_record ) {
			$id = $db_page_record->ID;

			$ee_page_id  = $db_page_record->ee_page_id;
			$post_status = $db_page_record->post_status;
			// is_built_with_wb4wp.
			if ( ! empty( $ee_page_id ) ) {
					$express_editor_page_id_to_wordpress_page_id_mapping[ $ee_page_id ] = array(
						'ee_id'          => $ee_page_id,
						'wp_id'          => $id,
						'can_be_deleted' => true,
						'post_status'    => $post_status,
					);
			} else {
				$express_editor_page_id_to_wordpress_page_id_mapping[ $id ] = array(
					'wp_id'          => $id,
					'can_be_deleted' => false,
					'post_status'    => $post_status,
				);
			}
		}

		return $express_editor_page_id_to_wordpress_page_id_mapping;
	}

	private static function save_navigation( $navigation_items, $pages, $saved_pages ) {
		// Steps to modify the navigation.
		// a. Make sure that the "nav menu" exists from this provider.
		// b. iterate over the navigation menu item.

		// Lets do it.

		// a. Make sure that the "nav menu" exists from this provider.
		$provider_name = Provider_Helper::get_provider_name();
		wp_delete_nav_menu( $provider_name );
		$menu_id = wp_create_nav_menu( $provider_name );

		$navigation_items_added_by_ee_navigation_item_id = array();

		// b. iterate over the navigation menu item.
		foreach ( $navigation_items as $nav_item ) {

			$page_id   = isset( $nav_item['pageId'] ) ? $nav_item['pageId'] : '';
			$parent_id = isset( $nav_item['parentId'] ) ? $nav_item['parentId'] : '0';

			// if the ['menuItem']['showInNavigation'] doesnt exist it should be showInNavigation (default value).
			$show_in_navigation = isset( $nav_item['menuItem']['showInNavigation'] ) ? $nav_item['menuItem']['showInNavigation'] : true;

			// skip hidden pages.
			if ( 'hidden' === $parent_id || false === $show_in_navigation ) {
				continue;
			}

			$wp_nav_item_parent_id = 0;

			if ( isset( $navigation_items_added_by_ee_navigation_item_id[ $parent_id ] ) ) {
				$wp_nav_item_parent_id = $navigation_items_added_by_ee_navigation_item_id[ $parent_id ];
			}

			// add nav items for pages that are not created with the express editor.
			if ( empty( $page_id ) ) {
				$nav_item_title = $nav_item['menuItem']['title'];
				$wp_id          = isset( $nav_item['wpID'] ) ? $nav_item['wpID'] : null;

				// the menu item is a WordPress page that is used in the main menu.
				if ( ! empty( $wp_id ) ) {
					// This need to be an link to an WP Page.
					$new_navigation_item_id = wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $nav_item_title,
							'menu-item-object-id' => $wp_id,
							'menu-item-object'    => 'page',
							'menu-item-status'    => 'publish',
							'menu-item-type'      => 'post_type',
							'menu-item-parent-id' => $wp_nav_item_parent_id,
						)
					);

					$navigation_items_added_by_ee_navigation_item_id[ $nav_item['id'] ] = $new_navigation_item_id;

					continue;
				}

				// it will be an external url (custom link)
				// page id is empty so it must be an "urlPath".
				$new_navigation_item_id = wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $nav_item_title,
						'menu-item-url'       => $nav_item['menuItem']['uriPath'],
						'menu-item-status'    => 'publish',
						'menu-item-type'      => 'custom',
						'menu-item-parent-id' => $wp_nav_item_parent_id,
					)
				);

				$navigation_items_added_by_ee_navigation_item_id[ $nav_item['id'] ] = $new_navigation_item_id;

				continue;
			}

			// its a normal page.
			$site_description_page_obj = current(
				array_filter(
					$pages,
					function ( $page ) use ( $page_id ) {
						return isset( $page['id'] ) && $page['id'] === $page_id;
					}
				)
			);

			if ( empty( $site_description_page_obj ) ) {
				Raygun_Manager::get_instance()->exception_handler( new Exception( 'Site page description is empty, couldn\'t create nav items.' ) );
			}

			$show_in_nav = isset( $site_description_page_obj['showInNavigation'] ) ? $site_description_page_obj['showInNavigation'] : true;
			if ( false === $show_in_nav ) {
				continue;
			}

			$saved_page_obj = current(
				array_filter(
					$saved_pages,
					function ( $saved_page ) use ( $page_id ) {
						return isset( $saved_page['ee_id'] ) && $saved_page['ee_id'] === $page_id;
					}
				)
			);

			if ( empty( $saved_page_obj ) ) {
				Raygun_Manager::get_instance()->exception_handler( new Exception( 'Saved site page description is empty, couldn\'t create nav items.' ) );
			}

			$new_navigation_item_id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $site_description_page_obj['title'],
					'menu-item-object-id' => $saved_page_obj['wp_id'],
					'menu-item-object'    => 'page',
					'menu-item-status'    => 'publish',
					'menu-item-type'      => 'post_type',
					'menu-item-parent-id' => $wp_nav_item_parent_id,
				)
			);

			$navigation_items_added_by_ee_navigation_item_id[ $nav_item['id'] ] = $new_navigation_item_id;
		}

		$locations          = get_theme_mod( 'nav_menu_locations', array() );
		$locations['wb4wp'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Updates the theme options
	 *
	 * @param $site_description
	 *
	 * @return false|void
	 */
	public static function save_theme_options( $bulk_update_wp_options_table, $site_description, $published_pages ) {
		if ( empty( $site_description ) ) {
			return false;
		}

		// this is a fix for an old ticket.
		$theme                      = $site_description['theme'];
		$theme['fonts']['fontSize'] = (string) $theme['fonts']['fontSize'];

		$theme_mods = Site_Model_Helper::map_site_model_to_theme_options( $site_description );

		// merge the bulk_update_wp_options_table with the new theme modifications.
		return array_merge( $bulk_update_wp_options_table, $theme_mods );
	}

	private static function colors_to_hex( $colors ) {
		if ( count( $colors ) < 3 ) {
			return '';
		}

		return sprintf( '#%02x%02x%02x', $colors[0], $colors[1], $colors[2] );
	}

	private static function save_site_meta( $bulk_update_wp_options_table, $site_description, $site_name ) {
		if ( ! empty( $site_description['language'] ) ) {
			$bulk_update_wp_options_table[ WB4WP_SITE_LANGUAGE ] = $site_description['language'];
		}

		if ( ! empty( $site_description['globalBinding']['title'] ) ) {
			$bulk_update_wp_options_table[ WB4WP_SITE_TITLE ] = $site_description['globalBinding']['title'];
		}

		if ( ! empty( $site_description['globalBinding']['description'] ) ) {
			$bulk_update_wp_options_table[ WB4WP_SITE_DESCRIPTION ] = $site_description['globalBinding']['description'];
		}

		if ( ! empty( $site_description['metadata']['faviconUrl'] ) ) {
			$bulk_update_wp_options_table[ WB4WP_SITE_FAVICON ] = $site_description['metadata']['faviconUrl'];
		}

		if ( ! empty( $site_name ) ) {
				$bulk_update_wp_options_table['blogname'] = $site_name;
		}

		// Store appearance settigns for the bluehost team, they use it for data metrics and the bluerock interface.
		$appearance = array();
		$theme      = $site_description['theme'];

		if ( ! empty( $theme['fonts']['body']['name'] ) ) {
			$appearance['font_body'] = $theme['fonts']['body']['name'];
		}

		if ( ! empty( $theme['fonts']['heading']['name'] ) ) {
			$appearance['font_title'] = $theme['fonts']['heading']['name'];
		}

		if ( ! empty( $theme['colors'] ) ) {
			$colors = array();
			array_push( $colors, self::colors_to_hex( $theme['colors']['text'] ) );
			array_push( $colors, self::colors_to_hex( $theme['colors']['background'] ) );
			$accent = $theme['colors']['accent'];
			if ( is_array( $accent ) && count( $accent ) >= 2 ) {
				array_push( $colors, self::colors_to_hex( $accent[0] ) );
				array_push( $colors, self::colors_to_hex( $accent[1] ) );
			}

			$appearance['colors'] = $colors;
		}
		// phpcs:disable WordPress.WP.AlternativeFunctions.json_encode_json_encode
		$bulk_update_wp_options_table[ WB4WP_APPEARANCE ] = json_encode( $appearance );

		return $bulk_update_wp_options_table;
	}

}
