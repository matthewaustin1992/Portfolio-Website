<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Helpers\Base64_Helper;
use Wb4Wp\Helpers\Feature_Pages_Helper;
use Wb4Wp\Helpers\Id_Helper;
use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Post_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Helpers\Site_Helper;
use Wb4Wp\Helpers\Site_Model_Helper;
use Wb4Wp\Integrations\Exceptions\Theme_Not_Active_Exception;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Description_Manager
 * @package Wb4Wp\Managers
 */
final class Description_Manager {

	/**
	 * Will register all the hooks for the pages portion of the plugin.
	 */
	public function add_hooks() {
		// todo Do we really need this?
		add_filter( 'bloginfo', array( $this, 'set_blog_info' ), 10, 2 );

		add_filter( 'wb4wp_section_output', array( $this, 'output_generic_section' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'check_for_duplicate_blocks' ), 99, 2 );
	}

	public function check_for_duplicate_blocks( $data, $post_arr ) {
		$actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		if ( strpos( $actual_link, 'wp/v2/pages' ) === false ) {
			return $data;
		}

		$asset_file = ABSPATH . 'wp-content/uploads/wb4wp-page-assets/assets_' . $post_arr['ID'] . '.json';

		if ( ! file_exists( $asset_file ) ) {
			return $data;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$assets       = json_decode( file_get_contents( $asset_file ), true );
		$blocks       = parse_blocks( wp_unslash( $data['post_content'] ) );
		$current_page = current(
			array_filter(
				$assets['siteModel']['pages'],
				function ( $page ) use ( $post_arr ) {
					$wp_page = get_page_by_path( $page['uriPath'], ARRAY_A );
					return $wp_page['ID'] === $post_arr['ID'];
				}
			)
		);

		if ( empty( $current_page ) ) {
			return $data;
		}

		$valid_blocks = array_filter(
			$blocks,
			function ( $b ) {
				return ! empty( $b['blockName'] );
			}
		);

		$data['post_content'] = $this->check_and_replace_block_ids( $current_page, $valid_blocks, $assets, $asset_file );

		return $data;
	}

	private function remove_unused_blocks( $current_page, $mapped_blocks ) {
		foreach ( $current_page['sections'] as $k => $section ) {
			if ( ! in_array( $section['id'], $mapped_blocks, true ) ) {
				unset( $current_page['sections'][ $k ] );
			}
		}

		return $current_page;
	}

	private function check_and_replace_block_ids( $current_page, $blocks, $assets, $asset_file ) {
		$content = '';

		$mapped_blocks = array_map(
			function ( $b ) {
				if (empty($b['attrs']['id'])) {
					return null;
				}
				return $b['attrs']['id'];
			},
			$blocks
		);

		foreach ( $blocks as &$block ) {
			if ( !empty($block['attrs']['id']) && count( array_keys( $mapped_blocks, $block['attrs']['id'], true ) ) > 1 ) {
				$block = $this->fix_duplicate_block( $blocks, $current_page, $block );
			}

			$content .= serialize_block( $block );
		}

		$page_site_model_index                                  = array_search( $current_page['id'], array_column( $assets['siteModel']['pages'], 'id' ), true );
		$assets['siteModel']['pages'][ $page_site_model_index ] = $this->remove_unused_blocks( $current_page, $mapped_blocks );

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		file_put_contents( $asset_file, wp_json_encode( $assets ) );
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents

		return $content;
	}

	private function fix_duplicate_block( $blocks, $current_page, &$block ) {
		$random_id = ( (int) $block['attrs']['id'] + wp_rand( count( $blocks ), 99999 ) );

		foreach ( $current_page['sections'] as $section ) {
			if ( $section['id'] === $block['attrs']['id'] ) {
				$new_section                = $section;
				$new_section['id']          = $random_id;
				$current_page['sections'][] = $new_section;
			}
		}

		foreach ( $block['innerBlocks'] as &$inner_block ) {
			$find = array(
				'section' . $block['attrs']['id'],
				'section-' . $block['attrs']['id'],
			);

			$replacements = array(
				'section' . $random_id,
				'section-' . $random_id,
			);

			$inner_block['innerHTML']    = str_replace( $find, $replacements, $inner_block['innerHTML'] );
			$inner_block['innerContent'] = str_replace( $find, $replacements, $inner_block['innerContent'] );
		}

		$block['attrs']['id'] = $random_id;

		return $block;
	}

	/**
	 * Hooks into the bloginfo function
	 *
	 * @param $text
	 * @param $show
	 *
	 * @return false|mixed|void
	 */
	public function set_blog_info( $text, $show ) {
		if ( 'name' === $show ) {
			$title = Options_Helper::get( WB4WP_SITE_TITLE, '' );
			if ( ! empty( $title ) ) {
				return $title;
			}
		} elseif ( 'description' === $show ) {
			$description = Options_Helper::get( WB4WP_SITE_DESCRIPTION, '' );
			if ( ! empty( $description ) ) {
				return $description;
			}
		}

		return $text;
	}

	public function handle_description_get() {
		try {
			$pages = $this->get_pages();

			if ( empty( $pages ) ) {
				return array( 'success' => false );
			}

			/**
			 * Site Description schema for Swagger
			 *
			 * @OA\Schema (
			 *  schema="SiteDescription",
			 *  type="object",
			 *  description="The site description model",
			 *  @OA\Property (
			 *   property="globalSections",
			 *   ref="#/components/schemas/GlobalSections",
			 *  ),
			 *  @OA\Property (
			 *   property="theme",
			 *   ref="#/components/schemas/ThemeObject",
			 *  ),
			 *  @OA\Property (
			 *   property="globalBinding",
			 *   ref="#/components/schemas/GlobalBinding",
			 *  ),
			 *  @OA\Property (
			 *   property="metadata",
			 *   ref="#/components/schemas/MetaData",
			 *  ),
			 *  @OA\Property (
			 *   property="pages",
			 *   ref="#/components/schemas/EE_Pages",
			 *  ),
			 *  @OA\Property (
			 *   property="navigation",
			 *   ref="#/components/schemas/EE_Navigation",
			 *  ),
			 *  @OA\Property (
			 *   property="apiKeys",
			 *   type="object",
			 *  ),
			 *  @OA\Property (
			 *   property="urls",
			 *   type="object",
			 *  ),
			 *  @OA\Property (
			 *   property="featureStorage",
			 *   type="object",
			 *  ),
			 *  @OA\Property (
			 *   property="globalFeatureModel",
			 *   type="object",
			 *  ),
			 *  @OA\Property (
			 *   property="language",
			 *   type="string",
			 *  ),
			 * )
			 */
			$metadata    = Site_Helper::get_metadata();
			$description = array(
				'globalSections'     => Site_Helper::get_global_sections(),
				'theme'              => Site_Helper::get_theme(),
				'globalBinding'      => Site_Helper::get_global_binding(),
				'metadata'           => $metadata,
				'pages'              => $pages,
				'navigation'         => $this->get_navigation(),
				'apiKeys'            => Site_Helper::get_option_as_array( WB4WP_API_KEYS ),
				'urls'               => Site_Helper::get_option_as_array( WB4WP_URLS ),
				'featureStorage'     => Site_Helper::get_option_as_array( WB4WP_FEATURE_STORAGE ),
				'globalFeatureModel' => Site_Helper::get_option_as_array( WB4WP_GLOBAL_FEATURE_MODEL ),
				'language'           => 'en-US',
			);

			try {
				Site_Model_Helper::apply_theme_options_to_site_model( $description );
			} catch ( Theme_Not_Active_Exception $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			}

			/**
			 * Site Model schema for Swagger
			 *
			 * @OA\Schema (
			 *  schema="SiteModel",
			 *  type="object",
			 *  description="The site model",
			 *  @OA\Property (
			 *   property="description",
			 *   ref="#/components/schemas/SiteDescription",
			 *  ),
			 *  @OA\Property (
			 *   property="site_name",
			 *   type="string",
			 *   description="The website title",
			 *  ),
			 *  @OA\Property (
			 *   property="subdomain",
			 *   type="string",
			 *   description="The subdomain name",
			 *  ),
			 *  @OA\Property (
			 *   property="is_auto_save",
			 *   type="boolean",
			 *  ),
			 *  @OA\Property (
			 *   property="is_published",
			 *   type="boolean",
			 *  ),
			 *  @OA\Property (
			 *   property="version_id",
			 *   type="integer",
			 *  ),
			 *  @OA\Property (
			 *   property="date_time",
			 *   type="string",
			 *  ),
			 *  @OA\Property (
			 *   property="account_id",
			 *   type="integer",
			 *  ),
			 *  @OA\Property (
			 *   property="screenshot",
			 *   type="string",
			 *  ),
			 *  @OA\Property (
			 *   property="free_domain",
			 *   type="string",
			 *  ),
			 *  @OA\Property (
			 *   property="domains",
			 *   type="array",
			 *   @OA\Items (
			 *    type="string",
			 *   ),
			 *  ),
			 * )
			 */
			return array(
				'description'  => $description,
				'site_name'    => ! empty( $metadata['siteName'] ) ? $metadata['siteName'] : '',
				'site_type'    => 3,
				'subdomain'    => 'f08b6af3c60b4037ba6a35dbbdab4283',
				'is_auto_save' => false,
				'is_published' => false,
				'version_id'   => 142122,
				'date_time'    => '2020-03-12T09:46:12',
				'account_id'   => 28324472,
				'screenshot'   => 'https://storage.googleapis.com/wzresponsiveeditor-publish-latest/screenshots/45/45579192/800x500.png',
				'free_domain'  => '{siteName}.websitebuilder-express.latest.wzdev.co',
				'domains'      => array(),
			);
		} catch ( Exception $ex ) {
			Raygun_Manager::get_instance()->exception_handler( $ex );

			throw $ex;
		}
	}

	public function get_pages() {
		$pages         = get_pages( array( 'post_status' => array( 'draft', 'publish' ) ) );
		$ee_pages      = array();
		$had_main_page = false;

		foreach ( $pages as $page ) {
			$is_main_page = (int) get_option( 'page_on_front', - 1 ) === $page->ID;
			$name         = $page->post_title;
			$id           = $page->ID;

			if ( ! Post_Helper::is_built_with_wb4wp( $id ) ) {
				continue;
			}

			if ( $is_main_page ) {
				$had_main_page = true;
			}

			$revisions = wp_get_post_revisions( $id );
			if ( ! empty( $revisions ) ) {
				$wp_post = current( $revisions );
			} else {
				$wp_post = get_post( $id );
			}

			$post_content = $wp_post->post_content;

			$ee_id    = get_post_meta( $id, 'WB4WP_PAGE_ID', true );
			$sections = $this->get_json_from_post_content( $post_content, $id );

			$match_uri_to_title = get_post_meta( $id, 'WB4WP_PAGE_MATCH_URI_TO_TITLE', true );

			if ( '' === $ee_id ) {
				$ee_id = Id_Helper::get_next_id();
				update_post_meta( $id, 'WB4WP_PAGE_ID', $ee_id );
			}

			if ( '' === $match_uri_to_title ) {
				$match_uri_to_title = false;
				update_post_meta( $id, 'WB4WP_PAGE_MATCH_URI_TO_TITLE', $match_uri_to_title );
			}

			/**
			 * EE_Page schema for Swagger
			 *
			 * @OA\Schema (
			 *  schema="EE_Page",
			 *  type="object",
			 *  description="Express Editor page object",
			 *  @OA\Property (
			 *   property="sections",
			 *   type="array",
			 *   @OA\Items (
			 *    type="string",
			 *   ),
			 *  ),
			 *  @OA\Property (
			 *   property="mainPage",
			 *   type="boolean",
			 *  ),
			 *  @OA\Property (
			 *   property="name",
			 *   type="string",
			 *  ),
			 *  @OA\Property (
			 *   property="id",
			 *   type="integer",
			 *  ),
			 *  @OA\Property (
			 *   property="wp_id",
			 *   type="integer",
			 *  ),
			 *  @OA\Property (
			 *   property="uriPath",
			 *   type="string",
			 *  ),
			 *  @OA\Property (
			 *   property="title",
			 *   type="string",
			 *  ),
			 *  @OA\Property (
			 *   property="matchUriToTitle",
			 *   type="boolean"
			 *  ),
			 *  @OA\Property (
			 *   property="showInNavigation",
			 *   type="boolean",
			 *  ),
			 *  @OA\Property (
			 *   property="headerSection",
			 *   type="object",
			 *  ),
			 * )
			 */
			array_push(
				$ee_pages,
				array(
					'sections'         => $sections,
					'mainPage'         => $is_main_page,
					'name'             => $name,
					'id'               => $ee_id,
					'wp_id'            => $id,
					'uriPath'          => $page->post_name,
					'title'            => $name,
					'matchUriToTitle'  => $match_uri_to_title,
					'pageType'         => get_post_meta( $id, 'WB4WP_PAGE_TYPE', true ),
					'showInNavigation' => true,
					'headerSection'    => Site_Helper::get_header_section(),
				)
			);
		}

		if ( ! $had_main_page && ! empty( $ee_pages[0] ) ) {
			$ee_pages[0]['mainPage'] = true;
		}

		$main_page = array_column( $ee_pages, 'mainPage' );
		array_multisort( $main_page, SORT_DESC, $ee_pages );

		/**
		 * EE_Pages schema for Swagger
		 *
		 * @OA\Schema (
		 *  schema="EE_Pages",
		 *  type="array",
		 *  description="The Express Editor pages",
		 *  @OA\Items (
		 *   ref="#/components/schemas/EE_Page",
		 *  ),
		 * )
		 */
		return $ee_pages;
	}

	/**
	 * Find the json data in the post content
	 *
	 * @param $post_content
	 * @param null $id
	 *
	 * @return mixed|string
	 */
	private function get_json_from_post_content( $post_content, $id = null ) {
		/**
		 * Backwards compatibility with <1.2
		 */
		if ( isset( $id ) ) {
			$sections          = get_post_meta( $id, 'WB4WP_PAGE_SECTIONS', true );
			$revision_sections = get_post_meta( $id, 'WB4WP_DRAFT_SECTIONS', true );

			if ( ! empty( $revision_sections ) ) {
				return $revision_sections;
			}
			if ( ! empty( $sections ) ) {
				return $sections;
			}
		}

		$sections = array();

		$html_sections = parse_blocks( $post_content );

		foreach ( $html_sections as $html_section ) {
			if ( empty( $html_section['blockName'] ) ) {
				continue;
			}

			switch ( $html_section['blockName'] ) {
				case 'wb4wp/block-generic-section':
					if ( ! empty( $html_section['attrs']['section_data'] ) ) { // Backwards-compatibility.
						$sections[] = $html_section['attrs']['section_data'];
					} else {
						$section_data = $html_section['attrs'];

						foreach ( array( 'layout', 'binding' ) as $property_to_decode ) {
							if ( empty( $section_data[ $property_to_decode ] ) ) {
								continue;
							}

							$property = &$section_data[ $property_to_decode ];
							$property = Base64_Helper::base64_decode_recursive( $property );
						}

						$sections[] = $section_data;
					}
					break;

				case 'wb4wp/container':
					$background = isset( $html_section ['attrs']['background'] )
						? base64_decode( $html_section ['attrs']['background'] ) // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
						: '{}';

					$sections[] = array(
						'id'       => isset( $html_section ['attrs']['id'] ) ? $html_section ['attrs']['id'] : uniqid(),
						'type'     => 'gutenberg',
						'category' => 'gutenberg',
						'layout'   => array(
							'section' => array(
								'id' => 'gutenberg',
							),
						),
						'binding'  => array(
							'background' => json_decode( $background ),
							'content'    => serialize_blocks( $html_section ['innerBlocks'] ),
						),
					);
					break;

				default:
					$sections[] = serialize_block( $html_section );
					break;
			}
		}

		return $sections;
	}

	public function get_navigation() {
		$pages         = get_pages( array( 'post_status' => array( 'draft', 'publish' ) ) );
		$ee_navigation = array();

		foreach ( $pages as $page ) {
			$id        = $page->ID;
			$ee_nav_id = get_post_meta( $id, 'WB4WP_NAV_ID', true );

			if ( '' === $ee_nav_id ) {
				$ee_nav_id = Id_Helper::get_next_id();
				update_post_meta( $id, 'WB4WP_NAV_ID', $ee_nav_id );
			}

			if ( Post_Helper::is_built_with_wb4wp( $id ) ) {
				$ee_id     = get_post_meta( $id, 'WB4WP_PAGE_ID', true );
				$ee_nav_id = get_post_meta( $id, 'WB4WP_NAV_ID', true );

				array_push(
					$ee_navigation,
					array(
						'id'       => (int) $ee_nav_id,
						'pageId'   => $ee_id,
						'wpID'     => $id,
						'parentId' => 'hidden',
					)
				);
			} else {
				$name = $page->post_title;
				$url  = get_page_link( $id );

				/**
				 * EE_Navigation_Item schema for Swagger
				 *
				 * @OA\Schema (
				 *  schema="EE_Navigation_Item",
				 *  type="object",
				 *  description="The Express Editor navigation object",
				 *  @OA\Property (
				 *   property="id",
				 *   type="integer",
				 *  ),
				 *  @OA\Property (
				 *   property="title",
				 *   type="string",
				 *  ),
				 *  @OA\Property (
				 *   property="order",
				 *   type="integer",
				 *  ),
				 *  @OA\Property (
				 *   property="menuItem",
				 *   type="object",
				 *   @OA\Property (
				 *    property="title",
				 *    type="string",
				 *   ),
				 *   @OA\Property (
				 *    property="uriPath",
				 *    type="string",
				 *   ),
				 *   @OA\Property (
				 *    property="showInNavigation",
				 *    type="boolean",
				 *   ),
				 *   @OA\Property (
				 *    property="externalUrlNewTab",
				 *    type="boolean",
				 *   ),
				 *   @OA\Property (
				 *    property="mainlink",
				 *    type="boolean",
				 *   ),
				 *  ),
				 * )
				 */
				array_push(
					$ee_navigation,
					array(
						'id'       => (int) $ee_nav_id,
						'wpID'     => $id,
						'title'    => $name,
						'menuItem' => array(
							'title'             => $name,
							'uriPath'           => $url,
							'showInNavigation'  => false,
							'externalUrlNewTab' => false,
							'mainlink'          => false,
						),
					)
				);
			}
		}

		$menus      = wp_get_nav_menus();
		$menu_index = array_search( Provider_Helper::get_provider_name(), array_column( $menus, 'name' ), true );
		if ( false !== $menu_index ) {
			$menu       = $menus[ $menu_index ];
			$menu_items = wp_get_nav_menu_items( $menu );

			// Add all external links to the result.
			foreach ( $menu_items as $k => &$menu_item ) {
				if ( 'custom' === $menu_item->object ) {
					array_push(
						$ee_navigation,
						array(
							'title'    => $menu_item->title,
							'id'       => $menu_item->ID,
							'menuItem' => array(
								'title'             => $menu_item->title,
								'uriPath'           => $menu_item->url,
								'showInNavigation'  => true,
								'externalUrlNewTab' => true,
								'mainlink'          => false,
							),
						)
					);
				}
			}

			$order = 1;
			foreach ( $menu_items as $k => &$menu_item ) {
				if ( 'custom' === $menu_item->object ) {
					$ee_navigation_item_index = array_search( $menu_item->ID, array_column( $ee_navigation, 'id' ), true );
				} else {
					$ee_id                    = intval( $menu_item->object_id );
					$ee_navigation_item_index = array_search( $ee_id, array_column( $ee_navigation, 'wpID' ), true );
				}

				if ( false !== $ee_navigation_item_index || 'custom' === $menu_item->object ) {
					// Check if menu item is a sub child.
					$parent_id = intval( $menu_item->menu_item_parent );

					if ( $parent_id > 0 ) {
						// Find the parent item in the menu items array.
						$parent_index = array_search( $parent_id, array_column( $menu_items, 'ID' ), true );
						if ( false !== $parent_index ) {
							$parent_item = $menu_items[ $parent_index ];
							// Use the object id to find that element in the EE navigation object.
							$ee_parent_id                    = intval( $parent_item->object_id );
							$ee_parent_navigation_item_index = array_search( $ee_parent_id, array_column( $ee_navigation, 'wpID' ), true );
							// Set the parent id of the EE navigation element.
							if ( false !== $ee_parent_navigation_item_index ) {
								$ee_navigation[ $ee_navigation_item_index ]['parentId'] = $ee_navigation[ $ee_parent_navigation_item_index ]['id'];
							} else {
								$ee_navigation[ $ee_navigation_item_index ]['parentId'] = $parent_id;
							}
						}
					} else {
						unset( $ee_navigation[ $ee_navigation_item_index ]['parentId'] );
					}

					$ee_navigation[ $ee_navigation_item_index ]['order'] = $order ++;

					if ( ! empty( $ee_navigation[ $ee_navigation_item_index ]['menuItem'] ) ) {
						$ee_navigation[ $ee_navigation_item_index ]['menuItem']['showInNavigation'] = true;
					}
				}
			}
		} else {
			foreach ( $ee_navigation as $k => $nav_item ) {
				unset( $ee_navigation[ $k ]['parentId'] );
			}
		}

		/**
		 * EE_Navigation schema for Swagger
		 *
		 * @OA\Schema (
		 *  schema="EE_Navigation",
		 *  type="array",
		 *  description="The array of Express Editor navigation items",
		 *  @OA\Items (
		 *   ref="#/components/schemas/EE_Navigation_Item",
		 *  ),
		 * )
		 */
		return $ee_navigation;
	}

	public function handle_feature_pages_get() {
		return Feature_Pages_Helper::get_supported_feature_pages();
	}

	public static function handle_feature_pages_preview_get( WP_REST_Request $request ) {
		$params = $request->get_params();

		if ( ! array_key_exists( 'page_type', $params ) ) {
			return array();
		}

		$preview_url = Feature_Pages_Helper::get_feature_page_preview_url( $params['page_type'] );

		if ( empty( $preview_url ) ) {
			return new WP_REST_Response(
				array(
					'success' => 'false',
				),
				404
			);
		}

		return new WP_REST_Response(
			array(
				'success'     => 'true',
				'preview_url' => $preview_url,
			),
			200
		);
	}

	/**
	 * Adjust the output of the page content
	 * to make sure it works on the frontend
	 *
	 * @param $content
	 *
	 * @return string|string[]
	 */
	public function output_generic_section( $content ) {
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( "'", '"', $content );
		$content = html_entity_decode( $content, ENT_QUOTES );
		$content = str_replace( 'url("', "url('", $content );

		return $content;
	}
}
