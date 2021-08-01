<?php

namespace Wb4Wp\Managers;

use Exception;
use Wb4Wp\Helpers\Creative_Mail_Helper;
use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Helpers\Options_Helper;
use Wb4Wp\Helpers\Provider_Helper;
use Wb4Wp\Helpers\Request_Helper;
use Wb4Wp\Integrations\Theme_Integration;
use Wb4Wp\Mappers\Theme_Options_Mapper;
use Wb4Wp\Models\Attachment;
use Wb4Wp\Wb4wp;
use WP_Error;
use WP_REST_Response;

/**
 * Class Api_Manager
 * @package Wb4Wp\Managers
 */
final class Api_Manager {

	const API_NAMESPACE             = 'wb4wp/v1';
	const ROUTE_METHODS             = 'methods';
	const ROUTE_PATH                = 'path';
	const ROUTE_CALLBACK            = 'callback';
	const ROUTE_ARGS                = 'args';
	const ROUTE_PERMISSION_CALLBACK = 'permission_callback';
	const ROUTE_REQUIRES_ADMIN      = 'requires_admin';
	const ORIGIN                    = 'Origin';
	const SUCCESS                   = 'success';
	const HTTP_STATUS               = 'status';

	/**
	 * Will add all the hooks that are
	 * required to setup our plugin API.
	 */
	public function add_hooks() {
		add_action( 'rest_api_init', array( $this, 'add_rest_endpoints' ) );
	}

	/**
	 * Registers the custom REST endpoints
	 */
	public function add_rest_endpoints() {
		$routes = array(
			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/blog-categories",
			 *  tags={"Blog"},
			 *  summary="Returns a list of categories available for blog posts",
			 *  @OA\Parameter (
			 *    name="per_page",
			 *    in="query",
			 *    required=false,
			 *    description="How many items should be returned per page. Default: 5",
			 *  ),
			 *  @OA\Parameter (
			 *    name="page",
			 *    in="query",
			 *    required=false,
			 *    description="Which page of the pagination to return. Default: 1",
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="List returned.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/blog-categories',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => array( Wb4wp::get_instance()->get_blog_manager(), 'get_categories' ),
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/token",
			 *  tags={"Other"},
			 *  summary="Stores the new SSO JWT for Bluehost",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Token is saved",
			 *  )
			 * )
			 */
			array(
				self::ROUTE_PATH                => '/token',
				self::ROUTE_METHODS             => 'POST',
				self::ROUTE_CALLBACK            => array(
					Wb4wp::get_instance()->get_provision_manager(),
					'update_token',
				),
				self::ROUTE_REQUIRES_ADMIN      => false,
				self::ROUTE_PERMISSION_CALLBACK => function () {
					return $this->validate_callback();
				},
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/ping",
			 *  tags={"Other"},
			 *  summary="Public endpoint that can be used to verify the API is up and running",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Endpoint is working",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="pong",
			 *        type="boolean",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH           => '/ping',
				self::ROUTE_METHODS        => 'GET',
				self::ROUTE_CALLBACK       => function () {
					return new WP_REST_Response( array( 'pong' => true ) );
				},
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/fetch-stats",
			 *  tags={"Other"},
			 *  summary="Fetches the latest stats and stores them",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Stats fetched and stored successfully",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=500,
			 *    description="Failed to fetch the stats",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *        default=false,
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH                => '/fetch-stats',
				self::ROUTE_METHODS             => 'GET',
				self::ROUTE_CALLBACK            => array( Wb4wp::get_instance()->get_stats_manager(), 'fetch_stats' ),
				self::ROUTE_REQUIRES_ADMIN      => false,
				self::ROUTE_PERMISSION_CALLBACK => function () {
					return $this->validate_callback();
				},
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/callback",
			 *  tags={"Onboarding"},
			 *  summary="Stores the account information in the wp_options",
			 *  @OA\RequestBody (
			 *   description="The account information",
			 *   required=true,
			 *   @OA\JsonContent (
			 *    @OA\Property (
			 *     property="api_key",
			 *     type="string",
			 *    ),
			 *    @OA\Property (
			 *     property="account_id",
			 *     type="integer",
			 *    ),
			 *    @OA\Property (
			 *     property="site_id",
			 *     type="integer",
			 *    ),
			 *   ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns true",
			 *  ),
			 *  @OA\Response (
			 *    response=400,
			 *    description="Missing account details",
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH                => '/callback',
				self::ROUTE_METHODS             => 'POST',
				self::ROUTE_CALLBACK            => array(
					Wb4wp::get_instance()->get_instance_manager(),
					'handle_callback',
				),
				self::ROUTE_REQUIRES_ADMIN      => false,
				self::ROUTE_PERMISSION_CALLBACK => function () {
					return $this->validate_callback();
				},
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/start_onboarding",
			 *  tags={"Onboarding"},
			 *  summary="Sends the onboarding data to be stored in Cloudflare",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the onboarding_url",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *      ),
			 *      @OA\Property (
			 *        property="onboarding_url",
			 *        type="string",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=500,
			 *    description="Returns the response from the Cloudflare endpoint",
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/start_onboarding',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => array(
					Wb4wp::get_instance()->get_provision_manager(),
					'handle_start_onboarding_get',
				),
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/description",
			 *  tags={"Publishing"},
			 *  summary="Handles the retrieval of the site model",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the site model",
			 *    @OA\JsonContent (ref="#/components/schemas/SiteModel"),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/description',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => array(
					Wb4wp::get_instance()->get_description_manager(),
					'handle_description_get',
				),
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/feature-pages",
			 *  tags={"Blog"},
			 *  summary="Returns the array of pages by the supported features",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the array of pages by the supported features",
			 *    @OA\JsonContent (
			 *      @OA\Items (
			 *        type="object",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/feature-pages',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => array(
					Wb4wp::get_instance()->get_description_manager(),
					'handle_feature_pages_get',
				),
			),

			array(
				self::ROUTE_PATH     => '/feature-pages/preview/(?P<page_type>.*)?',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => function ( $request ) {
					return Description_Manager::handle_feature_pages_preview_get( $request );
				},
				self::ROUTE_ARGS     => array(
					'page_type' => array(),
				),
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/wp_posts",
			 *  tags={"Blog"},
			 *  summary="Returns a paginated list of blog posts",
			 *  @OA\Parameter (
			 *    name="per_page",
			 *    in="query",
			 *    required=false,
			 *    description="How many items should be returned per page. Default: 10",
			 *  ),
			 *  @OA\Parameter (
			 *    name="page",
			 *    in="query",
			 *    required=false,
			 *    description="Which page of the pagination to return. Default: 1",
			 *  ),
			 *  @OA\Parameter (
			 *    name="category",
			 *    in="query",
			 *    required=false,
			 *    description="Which categories to show posts for. Can be a comma-separated list of category ids or a single number. Default: 0 (all)",
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the array of blog posts",
			 *    @OA\JsonContent (
			 *      @OA\Items (
			 *        type="object",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH           => '/wp_posts',
				self::ROUTE_METHODS        => 'GET',
				self::ROUTE_CALLBACK       => array( Wb4wp::get_instance()->get_blog_manager(), 'get_blog_posts' ),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/wp_posts/:id",
			 *  tags={"Blog"},
			 *  summary="Returns a blog post, if found",
			 *  @OA\Response (
			 *    response=404,
			 *    description="Returns an indication that the blog post could not be found.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=false,
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the blog post.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=true,
			 *      ),
			 *      @OA\Property (
			 *         property="blog_post",
			 *         type="object",
			 *      )
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH           => 'wp_posts/(?P<id>\d+)',
				self::ROUTE_METHODS        => 'GET',
				self::ROUTE_CALLBACK       => array( Wb4wp::get_instance()->get_blog_manager(), 'get_blog_post' ),
				self::ROUTE_ARGS           => array(
					'id' => array(),
				),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/save_site",
			 *  tags={"Saving"},
			 *  summary="Saves the site (as draft).",
			 *  @OA\Parameter (
			 *    name="HTTP_X_API_KEY",
			 *    in="header",
			 *    required=true,
			 *  ),
			 *  @OA\RequestBody (
			 *   description="The site model",
			 *   required=true,
			 *   @OA\JsonContent (
			 *    @OA\Property (
			 *     property="site_description",
			 *     type="object",
			 *    ),
			 *   ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned when saved successfully"
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH           => '/save_site',
				self::ROUTE_METHODS        => 'POST',
				self::ROUTE_CALLBACK       => function ( $request ) {
					return Save_Site_Manager::handle_save_site( $request );
				},
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/images",
			 *  tags={"Other"},
			 *  summary="Retrieves the WordPress images",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns an array of image attachments",
			 *    @OA\JsonContent (
			 *      @OA\Items (
			 *        type="object",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH                => '/images',
				self::ROUTE_METHODS             => 'GET',
				self::ROUTE_REQUIRES_ADMIN      => false,
				self::ROUTE_PERMISSION_CALLBACK => function () {
					return $this->validate_api_key();
				},
				self::ROUTE_CALLBACK            => function () {
					try {
						$attachment_data = array();
						$attachments     = get_posts(
							array(
								'post_type'      => 'attachment',
								'post_mime_type' => 'image',
								'post_status'    => 'inherit',
								'posts_per_page' => - 1,
							)
						);

						foreach ( $attachments as $attachment ) {
							array_push( $attachment_data, new Attachment( $attachment ) );
						}

						return new WP_REST_Response( $attachment_data, 200 );
					} catch ( Exception $ex ) {
						Raygun_Manager::get_instance()->exception_handler( $ex );

						throw $ex;
					}
				},
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/provision",
			 *  tags={"Onboarding"},
			 *  summary="Handles the provisioning of the plugin. Also sets the plugin status to 'provisioned'",
			 *  @OA\RequestBody (
			 *   required=false,
			 *   @OA\JsonContent (),
			 *  ),
			 *  @OA\Response (
			 *    response=500,
			 *    description="Returned when the provisioning failed",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *        default=false,
			 *      ),
			 *      @OA\Property (
			 *        property="auth_manager_url",
			 *        type="string",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned when provisioned successfully",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *      ),
			 *      @OA\Property (
			 *        property="signup_url",
			 *        type="string",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/provision',
				self::ROUTE_METHODS  => 'POST',
				self::ROUTE_CALLBACK => function () {
					try {
						delete_option( WB4WP_INSTANCE_UUID_KEY );

						if ( Provider_Helper::is_bluehost() ) {
							return Provider_Bluehost_Manager::provision( Options_Helper::get( WB4WP_JWT, '' ) );
						} else {
							delete_option( WB4WP_INSTANCE_UUID_KEY );

							return new WP_REST_Response(
								array(
									self::SUCCESS => true,
									'signup_url'  => Wb4wp::get_instance()->get_wb4wp_manager()->get_signup_url(),
								)
							);
						}
					} catch ( Exception $ex ) {
						Raygun_Manager::get_instance()->exception_handler( $ex );

						throw $ex;
					}
				},
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/sso",
			 *  tags={"Onboarding"},
			 *  summary="Handles the sso auth of the plugin",
			 *  @OA\RequestBody (
			 *   required=false,
			 *   @OA\JsonContent (),
			 *  ),
			 *  @OA\Response (
			 *    response=500,
			 *    description="Returned when the sso failed",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *        default=false,
			 *      ),
			 *      @OA\Property (
			 *        property="auth_manager_url",
			 *        type="string",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned when the sso authenticated successfully",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="success",
			 *        type="boolean",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/sso',
				self::ROUTE_METHODS  => 'POST',
				self::ROUTE_CALLBACK => array( Wb4wp::get_instance()->get_provision_manager(), 'handle_sso' ),
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/plugin-state",
			 *  tags={"Onboarding"},
			 *  summary="Retrieves the WordPress images",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned if the plugin state was retrieved successfully",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *        property="plugin_state",
			 *        type="string",
			 *        enum={"new", "provisioned", "onboarded", "suspended"},
			 *      ),
			 *      @OA\Property (
			 *        property="has_onboarding_id",
			 *        type="boolean",
			 *      ),
			 *      @OA\Property (
			 *        property="has_debug_plugin_installed",
			 *        type="boolean",
			 *      ),
			 *      @OA\Property (
			 *        property="provider",
			 *        type="string",
			 *        enum={"bluehost", "websitebuilder"},
			 *      ),
			 *      @OA\Property (
			 *        property="env",
			 *        type="string",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/plugin-state',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => function () {
					try {
						$plugins = array_filter(
							apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
							function ( $plugin ) {
								return strpos( $plugin, 'wb4wp-debug-plugin' ) !== false;
							}
						);

						return new WP_REST_Response(
							array(
								'plugin_state'      => Options_Helper::get( WB4WP_PLUGIN_STATE, 'new' ),
								'has_onboarding_id' => ! empty( Options_Helper::get( WB4WP_ONBOARDING_ID ) ),
								'onboarding_id'     => Options_Helper::get( WB4WP_ONBOARDING_ID ),
								'has_debug_plugin_installed' => ! empty( $plugins ),
								'provider'          => Provider_Helper::get_provider(),
								'env'               => Environment_Helper::get_environment(),
							)
						);
					} catch ( Exception $ex ) {
						Raygun_Manager::get_instance()->exception_handler( $ex );

						throw $ex;
					}
				},
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/drafts",
			 *  tags={"Other"},
			 *  summary="Retrieves the WordPress draft pages",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned if the draft pages were retrieved successfully",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="pages",
			 *         type="array",
			 *         @OA\Items (
			 *           type="object",
			 *         ),
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/drafts',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => array( Wb4wp::get_instance()->get_word_press_manager(), 'get_draft_pages' ),
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/publish",
			 *  tags={"Onboarding"},
			 *  summary="Handles the publishing of the pages",
			 *  @OA\RequestBody (
			 *   required=true,
			 *   description="Requires an array of ID's to publish",
			 *   @OA\JsonContent (
			 *     @OA\Items (
			 *       type="integer",
			 *     ),
			 *   ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned when the pages are published successfully",
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/publish',
				self::ROUTE_METHODS  => 'POST',
				self::ROUTE_CALLBACK => array( Wb4wp::get_instance()->get_word_press_manager(), 'publish_pages' ),
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/delete_onboarding_id",
			 *  tags={"Onboarding"},
			 *  summary="Deletes the onboarding ID from WP_Options",
			 *  @OA\Response (
			 *    response=204,
			 *    description="Returned when the onboarding was removed successfully",
			 *  ),
			 *  @OA\Response (
			 *    response=500,
			 *    description="Returned when the onboarding was removed unsuccessfully",
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/delete_onboarding_id',
				self::ROUTE_METHODS  => 'POST',
				self::ROUTE_CALLBACK => array( Wb4wp::get_instance()->get_provision_manager(), 'delete_onboarding_id' ),
			),

			/**
			 * Swagger POST route
			 *
			 * @OA\Post (
			 *  path="/wb4wp/v1/set_creative_mail_referrer",
			 *  tags={"Other"},
			 *  summary="Adds a BHWB flag to the CE4WP_REFERRED_BY option",
			 *  @OA\Response (
			 *    response=204,
			 *    description="Returned when CE4WP_REFERRED_BY has been successfully modified or when it was already set",
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/set_creative_mail_referrer',
				self::ROUTE_METHODS  => 'POST',
				self::ROUTE_CALLBACK => function () {
					if ( Creative_Mail_Helper::has_bhwb_referrer() === false ) {
						Creative_Mail_Helper::set_bhwb_referrer();
					}

					return new WP_REST_Response( null, 204 );
				},
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/settings",
			 *  tags={"Theming"},
			 *  summary="Returns the theme's settings",
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returned if the provider's theme is installed, active, and supports querying of theme settings.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=true
			 *      ),
			 *      @OA\Property (
			 *         property="settings",
			 *         type="object",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=404,
			 *    description="Returned if the provider's theme does not support querying of theme settings, is not installed, or is inactive.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=false,
			 *      ),
			 *      @OA\Property (
			 *         property="message",
			 *         type="string",
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH     => '/settings',
				self::ROUTE_METHODS  => 'GET',
				self::ROUTE_CALLBACK => function ( $request ) {
					try {
						$page_type = $request->get_param( 'page_type' );
						$uuid      = $request->get_param( 'uuid' );

						$settings = Theme_Integration::get_customize_setting_list( $page_type );

						$settings_dto = Theme_Options_Mapper::to_dto( $settings, $uuid );

						return new WP_REST_Response(
							array(
								'success'  => true,
								'settings' => $settings_dto,
								'uuid'     => $uuid,
							),
							200
						);
					} catch ( Exception $exception ) {
						return new WP_REST_Response(
							array(
								'success' => false,
								'message' => $exception->getMessage(),
							),
							404
						);
					}
				},
			),

			array(
				self::ROUTE_PATH                => '/contacts',
				self::ROUTE_METHODS             => 'POST',
				self::ROUTE_CALLBACK            => array(
					Wb4wp::get_instance()->get_contacts_manager(),
					'contacts_upsert',
				),
				self::ROUTE_REQUIRES_ADMIN      => false,
				self::ROUTE_PERMISSION_CALLBACK => function () {
					return $this->validate_api_key();
				},
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/wc_products",
			 *  tags={"WooCommerce"},
			 *  summary="Returns a paginated list of WooCommerce products",
			 *  @OA\Parameter (
			 *    name="per_page",
			 *    in="query",
			 *    required=false,
			 *    description="How many items should be returned per page. Default: 10",
			 *  ),
			 *  @OA\Parameter (
			 *    name="page",
			 *    in="query",
			 *    required=false,
			 *    description="Which page of the pagination to return. Default: 1",
			 *  ),
			 *  @OA\Parameter (
			 *    name="category",
			 *    in="query",
			 *    required=false,
			 *    description="Which categories to show products for. A comma-separated list of category slugs.",
			 *  ),
			 *  @OA\Response (
			 *    response=405,
			 *    description="Returns a message saying 'The WooCommerce plugin is not active.'",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=false,
			 *      ),
			 *      @OA\Property (
			 *         property="message",
			 *         type="string",
			 *         default="The WooCommerce plugin is not active.",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the WooCommerce products.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="products",
			 *         type="array",
			 *         @OA\Items (
			 *           type="object",
			 *         ),
			 *      ),
			 *      @OA\Property (
			 *         property="page",
			 *         type="integer",
			 *      ),
			 *      @OA\Property (
			 *         property="total",
			 *         type="integer",
			 *      ),
			 *      @OA\Property (
			 *         property="max_num_pages",
			 *         type="integer",
			 *      ),
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=true,
			 *      ),
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH           => '/wc_products',
				self::ROUTE_METHODS        => 'GET',
				self::ROUTE_CALLBACK       => array(
					Wb4wp::get_instance()->get_woo_commerce_manager(),
					'get_products',
				),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			/**
			 * Swagger GET route
			 *
			 * @OA\Get (
			 *  path="/wb4wp/v1/wc_products/:id",
			 *  tags={"WooCommerce"},
			 *  summary="Returns a WooCommerce product, if found",
			 *  @OA\Response (
			 *    response=405,
			 *    description="Returns a message saying 'The WooCommerce plugin is not active.'",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=false,
			 *      ),
			 *      @OA\Property (
			 *         property="message",
			 *         type="string",
			 *         default="The WooCommerce plugin is not active.",
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=404,
			 *    description="Returns an indication that the WooCommerce product was not found.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=false,
			 *      ),
			 *    ),
			 *  ),
			 *  @OA\Response (
			 *    response=200,
			 *    description="Returns the WooCommerce product.",
			 *    @OA\JsonContent (
			 *      @OA\Property (
			 *         property="success",
			 *         type="boolean",
			 *         default=true,
			 *      ),
			 *      @OA\Property (
			 *         property="product",
			 *         type="object",
			 *      )
			 *    ),
			 *  ),
			 * )
			 */
			array(
				self::ROUTE_PATH           => '/wc_products/(?P<id>\d+)',
				self::ROUTE_METHODS        => 'GET',
				self::ROUTE_CALLBACK       => array( Wb4wp::get_instance()->get_woo_commerce_manager(), 'get_product' ),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			array(
				self::ROUTE_PATH           => '/disable-plugin',
				self::ROUTE_METHODS        => 'POST',
				self::ROUTE_CALLBACK       => array(
					Wb4wp::get_instance()->get_word_press_manager(),
					'disable_plugin',
				),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			array(
				self::ROUTE_PATH           => '/posts/(?P<post_id>\d+)/publish_autosave',
				self::ROUTE_METHODS        => 'POST',
				self::ROUTE_CALLBACK       => array( Autosave_Manager::class, 'handle_publish_autosave' ),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			array(
				self::ROUTE_PATH           => '/posts/(?P<post_id>\d+)/discard_autosave',
				self::ROUTE_METHODS        => 'POST',
				self::ROUTE_CALLBACK       => array( Autosave_Manager::class, 'handle_discard_autosave' ),
				self::ROUTE_REQUIRES_ADMIN => false,
			),

			array(
				self::ROUTE_PATH           => '/dont-show-unpublished-changes-modal-again',
				self::ROUTE_METHODS        => 'POST',
				self::ROUTE_CALLBACK       => array( Options_Helper::class, 'handle_dont_show_unpublished_changes_modal_again' ),
				self::ROUTE_REQUIRES_ADMIN => false,
			),
		);

		foreach ( $routes as $route ) {
			$this->register_route( $route );
		}
	}

	public function validate_callback() {
		nocache_headers();

		if ( ! array_key_exists( 'HTTP_X_API_KEY', $_SERVER ) ) {
			// phpcs:ignore
			return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.', WB4WP_WP_TEXT_DOMAIN ), array( self::HTTP_STATUS => 401 ) );
		}

		$api_key = $_SERVER['HTTP_X_API_KEY'];
		if ( Options_Helper::get_handshake_token() === $api_key ) {
			return true;
		}

		return new WP_Error( 'rest_unauthorized', 'Unauthorized', array( self::HTTP_STATUS => 401 ) );
	}

	/**
	 * Verify that the API KEY is in
	 * the header and that it matches
	 * our saved API KEY
	 *
	 * @return WP_Error|bool
	 */
	public function validate_api_key() {
		nocache_headers();

		if ( ! array_key_exists( 'HTTP_X_API_KEY', $_SERVER ) ) {
			return Request_Helper::throw_error(
			// phpcs:ignore
				__( 'Sorry, you are not allowed to do that.', WB4WP_WP_TEXT_DOMAIN ),
				__FILE__,
				__LINE__,
				401,
				true
			);
		}

		$key     = Options_Helper::get( WB4WP_INSTANCE_API_KEY_KEY, null, true );
		$api_key = $_SERVER['HTTP_X_API_KEY'];
		if ( $api_key === $key ) {
			return true;
		}

		return Request_Helper::throw_error(
		// phpcs:ignore
			__( 'Sorry, you are not allowed to do that.', WB4WP_WP_TEXT_DOMAIN ),
			__FILE__,
			__LINE__,
			401,
			true
		);
	}

	/**
	 * Registers a route to the WP Rest endpoints for this plugin.
	 *
	 * @param array $route
	 */
	private function register_route( array $route ) {
		$path           = $route[ self::ROUTE_PATH ];
		$methods        = $route[ self::ROUTE_METHODS ];
		$callback       = $route[ self::ROUTE_CALLBACK ];
		$args           = array_key_exists( self::ROUTE_ARGS, $route ) ? $route[ self::ROUTE_ARGS ] : array();
		$requires_admin = true;

		if ( isset( $route[ self::ROUTE_REQUIRES_ADMIN ] ) ) {
			$requires_admin = $route[ self::ROUTE_REQUIRES_ADMIN ];
		}

		if ( empty( $path ) ) {
			return;
		}

		$is_admin = current_user_can( 'administrator' );
		if ( ! $is_admin && $requires_admin ) {
			return;
		}

		$arguments = array(
			self::ROUTE_METHODS             => $methods,
			self::ROUTE_CALLBACK            => $callback,
			self::ROUTE_PERMISSION_CALLBACK => function () {
				return true;
			},
			self::ROUTE_ARGS                => $args,
		);

		if ( array_key_exists( self::ROUTE_PERMISSION_CALLBACK, $route ) ) {
			$arguments[ self::ROUTE_PERMISSION_CALLBACK ] = $route[ self::ROUTE_PERMISSION_CALLBACK ];
		}

		register_rest_route( self::API_NAMESPACE, $path, $arguments );
	}
}
