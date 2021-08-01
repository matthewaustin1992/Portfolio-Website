<?php

namespace Wb4Wp\Helpers;

use stdClass;
use WP_Customize_Manager;

/**
 * Class Site_Helper
 * @package Wb4Wp\Helpers
 */
final class Site_Helper {

	/**
	 * Gets a string representing the global section.
	 *
	 * @return string|array
	 */
	public static function get_global_sections() {
		/**
		 * Global Sections schema for Swagger
		 *
		 * @OA\Schema (
		 *  schema="GlobalSections",
		 *  type="object",
		 *  description="The global sections",
		 *  @OA\Property (
		 *   property="footer",
		 *   type="object",
		 *   @OA\Property (
		 *    property="id",
		 *    type="integer",
		 *   ),
		 *   @OA\Property (
		 *    property="layout",
		 *    type="object",
		 *    @OA\Property (
		 *     property="section",
		 *     type="object",
		 *     @OA\Property (
		 *      property="id",
		 *      type="string",
		 *     ),
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="category",
		 *    type="string",
		 *   ),
		 *   @OA\Property (
		 *    property="binding",
		 *    type="object",
		 *    @OA\Property (
		 *     property="title-navigation",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="title-social",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="description",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="title",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="titleNavigation",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="titleSocial",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="titleAddress",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="titleDescription",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="copyright",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="pagesMenu",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="_toggle",
		 *     type="object",
		 *     @OA\Property (
		 *      property="description",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="copyrightHelper",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="sitemapToggle",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="global.email",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="global.accounts",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="global.phone",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="logoToggle",
		 *      type="boolean",
		 *     ),
		 *     @OA\Property (
		 *      property="dividerToggle",
		 *      type="boolean",
		 *     ),
		 *    ),
		 *    @OA\Property (
		 *     property="addressToggle",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="background",
		 *     type="object",
		 *     @OA\Property (
		 *      property="colorIndex",
		 *      type="integer",
		 *     ),
		 *     @OA\Property (
		 *      property="patternIndex",
		 *      type="integer",
		 *     ),
		 *     @OA\Property (
		 *      property="opacity",
		 *      type="integer",
		 *     ),
		 *     @OA\Property (
		 *      property="none",
		 *      type="boolean",
		 *     ),
		 *    ),
		 *    @OA\Property (
		 *     property="copyrightHelper",
		 *     type="string",
		 *    ),
		 *   ),
		 *  ),
		 * )
		 */

		$val = get_option(
			WB4WP_GLOBAL_SECTIONS,
			array(
				'footer' => array(
					'id'       => 1,
					'layout'   =>
						array(
							'section' =>
								array(
									'id' => 'bajigu77',
								),
						),
					'category' => 'footers',
					'binding'  =>
						array(
							'title-navigation' => 'Pages',
							'title-social'     => 'Follow us',
							'description'      => '',
							'title'            => '',
							'titleNavigation'  => 'Pages',
							'titleSocial'      => 'Follow us',
							'titleAddress'     => 'Address',
							'titleDescription' => 'About us',
							'copyright'        => true,
							'pagesMenu'        => false,
							'_toggle'          =>
								array(
									'description'     => true,
									'copyrightHelper' => true,
									'sitemapToggle'   => true,
									'global.email'    => true,
									'global.accounts' => true,
									'global.phone'    => true,
									'logoToggle'      => true,
									'dividerToggle'   => true,
								),
							'addressToggle'    => true,
							'background'       =>
								array(
									'colorIndex'   => 1,
									'patternIndex' => 7,
									'opacity'      => 0,
									'none'         => true,
								),
							'copyrightHelper'  => '',
						),
				),
			)
		);

		if ( is_string( $val ) ) {
			return json_decode( $val, true );
		}

		return $val;
	}

	/**
	 * Gets a string representing the theme.
	 *
	 * @return string|array
	 */
	public static function get_theme() {
		/**
		 * Theme Object schema for Swagger
		 *
		 * @OA\Schema (
		 *  schema="ThemeObject",
		 *  type="object",
		 *  description="The theme settings",
		 *  @OA\Property (
		 *   property="colors",
		 *   type="object",
		 *   @OA\Property (
		 *    property="text",
		 *    type="array",
		 *    @OA\Items (
		 *     type="integer",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="background",
		 *    type="array",
		 *    @OA\Items (
		 *     type="integer",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="accent",
		 *    type="array",
		 *    @OA\Items (
		 *     type="array",
		 *     @OA\Items (
		 *      type="integer",
		 *     ),
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="style",
		 *    type="string",
		 *   ),
		 *  ),
		 *  @OA\Property (
		 *   property="fonts",
		 *   type="object",
		 *   @OA\Property (
		 *    property="body",
		 *    type="object",
		 *    @OA\Property (
		 *     property="name",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="weight",
		 *     type="string",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="heading",
		 *    type="object",
		 *    @OA\Property (
		 *     property="name",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="weight",
		 *     type="string",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="fontSize",
		 *    type="integer",
		 *   ),
		 *  ),
		 *  @OA\Property (
		 *   property="animations",
		 *   type="object",
		 *   @OA\Property (
		 *    property="enabled",
		 *    type="boolean",
		 *   ),
		 *  ),
		 *  @OA\Property (
		 *   property="roundedCorners",
		 *   type="boolean",
		 *  ),
		 * )
		 */
		$theme = get_option(
			WB4WP_THEME,
			array(
				'colors'         =>
					array(
						'text'       =>
							array( 246, 246, 246 ),
						'background' =>
							array( 255, 255, 255 ),
						'accent'     =>
							array(
								array( 250, 250, 250 ),
								array( 255, 255, 255 ),
							),
						'style'      => 'minimal',
					),
				'fonts'          =>
					array(
						'body'     =>
							array(
								'name'   => 'Lato',
								'weight' => '400',
							),
						'heading'  =>
							array(
								'name'   => 'Arvo',
								'weight' => '400',
							),
						'fontSize' => 1,
					),
				'animations'     =>
					array(
						'enabled' => false,
					),
				'roundedCorners' => false,
			)
		);

		if ( is_string( $theme ) ) {
			$theme = json_decode( $theme, true );
		}

		if ( ! empty( $theme['wordpressTheme']['uuid'] ) ) {
			$uuid              = $theme['wordpressTheme']['uuid'];
			$customizer        = new WP_Customize_Manager( array( 'changeset_uuid' => $uuid ) );
			$changeset_post_id = $customizer->changeset_post_id();

			$existing_status = get_post_status( $changeset_post_id );

			if ( 'publish' === $existing_status || 'trash' === $existing_status ) {
				$customizer                         = new WP_Customize_Manager();
				$theme['wordpressTheme']['uuid']    = $customizer->changeset_uuid();
				$theme['wordpressTheme']['changes'] = null;
			}
		}

		return $theme;
	}

	/**
	 * Gets a string representing the metadata.
	 *
	 * @return string|array
	 */
	public static function get_metadata() {
		/**
		 * Metadata schema for Swagger
		 *
		 * @OA\Schema (
		 *  schema="MetaData",
		 *  type="object",
		 *  description="The meta data object",
		 *  @OA\Property (
		 *   property="siteDescription",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="partnerId",
		 *   type="integer",
		 *  ),
		 *  @OA\Property (
		 *   property="siteKeywords",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="socialShareImage",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="googleAnalyticsKey",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="faviconUrl",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="siteName",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="siteDomain",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="siteHeaderHtml",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="siteFooterHtml",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="language",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="companyName",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="templateName",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="topicId",
		 *   type="integer",
		 *  ),
		 *  @OA\Property (
		 *   property="verticalId",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="addedPageTypes",
		 *   type="array",
		 *   @OA\Items (
		 *    type="string",
		 *   ),
		 *  ),
		 * )
		 */

		$val = get_option(
			WB4WP_METADATA,
			array(
				'siteDescription'    => '',
				'partnerId'          => 200,
				'siteKeywords'       => '',
				'socialShareImage'   => 'https://storage.googleapis.com/production-constantcontact-v1-0-1/901/207901/4di52sUo/2470b3163d1647b4aa451bcfce496a44',
				'googleAnalyticsKey' => '',
				'faviconUrl'         => '',
				'siteName'           => '',
				'siteDomain'         => '',
				'siteHeaderHtml'     => '',
				'siteFooterHtml'     => '',
				'language'           => 'en',
				'companyName'        => 'Peter',
				'templateName'       => 'business',
				'topicId'            => 321,
				'verticalId'         => '86b90fbc-5657-4393-ad6d-72d3c63d5391',
				'addedPageTypes'     => array(),
			)
		);

		if ( is_string( $val ) ) {
			$val = json_decode( $val, true );
		}

		return $val;
	}

	/**
	 * Gets a string representing the global binding.
	 *
	 * @return string|array
	 */
	public static function get_global_binding() {
		/**
		 * Global Binding schema for Swagger
		 *
		 * @OA\Schema (
		 *  schema="GlobalBinding",
		 *  type="object",
		 *  description="The global binding data",
		 *  @OA\Property (
		 *   property="accounts",
		 *   type="object",
		 *  ),
		 *  @OA\Property (
		 *   property="email",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="openingHours",
		 *   type="object",
		 *  ),
		 *  @OA\Property (
		 *   property="phone",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="companyName",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="logo",
		 *   type="object",
		 *   @OA\Property (
		 *    property="value",
		 *    type="string",
		 *   ),
		 *  ),
		 *  @OA\Property (
		 *   property="title",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="callToAction",
		 *   type="object",
		 *   @OA\Property (
		 *    property="source",
		 *    type="string",
		 *   ),
		 *   @OA\Property (
		 *    property="title",
		 *    type="string",
		 *   ),
		 *  ),
		 *  @OA\Property (
		 *   property="legal",
		 *   type="object",
		 *   @OA\Property (
		 *    property="privacyPolicy",
		 *    type="object",
		 *    @OA\Property (
		 *     property="showCookieBanner",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="bannerPosition",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="bannerText",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="agreeButtonText",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="privacyPolicyText",
		 *     type="string",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="termsOfService",
		 *    type="object",
		 *    @OA\Property (
		 *     property="termsOfServiceText",
		 *     type="string",
		 *    ),
		 *   ),
		 *  ),
		 *  @OA\Property (
		 *   property="description",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="coverPhoto",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="about",
		 *   type="string",
		 *  ),
		 * )
		 */
		$val = get_option(
			WB4WP_GLOBAL_BINDING,
			array(
				'accounts'     => array(
					'twitter'   => 'https://twitter.com/bluehost?utm_source=bluehost-website-builder&utm_medium=social-buttons&utm_campaign=default-value',
					'instagram' => 'https://www.instagram.com/bluehost/?utm_source=bluehost-website-builder&utm_medium=social-buttons&utm_campaign=default-value',
				),
				'email'        => get_bloginfo( 'admin_email' ),
				'openingHours' => new stdClass(),
				'phone'        => '',
				'companyName'  => '',
				'logo'         => array(
					'value' => '',
				),
				'title'        => wp_title( null, false ),
				'callToAction' => array(
					'source' => 'global.phone',
					'title'  => 'Call',
				),
				'legal'        => array(
					'privacyPolicy'  =>
						array(
							'showCookieBanner'  => true,
							'bannerPosition'    => 'bottom',
							'bannerText'        => 'This site uses cookies',
							'agreeButtonText'   => 'I\'m okay with that',
							'privacyPolicyText' => '',
						),
					'termsOfService' =>
						array(
							'termsOfServiceText' => '',
						),
				),
				'description'  => '',
				'coverPhoto'   => '',
				'about'        => '',
			)
		);

		if ( is_string( $val ) ) {
			$val = json_decode( $val, true );
		}

		return $val;
	}

	/**
	 * Gets a string representing the header section.
	 *
	 * @return string|array
	 */
	public static function get_header_section() {
		/**
		 * Header Section object for Swagger
		 *
		 * @OA\Schema (
		 *  schema="HeaderSection",
		 *  type="object",
		 *  description="The header section object",
		 *  @OA\Property (
		 *   property="id",
		 *   type="integer",
		 *  ),
		 *  @OA\Property (
		 *   property="layout",
		 *   type="object",
		 *   @OA\Property (
		 *    property="section",
		 *    type="object",
		 *    @OA\Property (
		 *     property="id",
		 *     type="string",
		 *    )
		 *   )
		 *  ),
		 *  @OA\Property (
		 *   property="category",
		 *   type="string",
		 *  ),
		 *  @OA\Property (
		 *   property="binding",
		 *   type="object",
		 *   @OA\Property (
		 *    property="title",
		 *    type="string",
		 *   ),
		 *   @OA\Property (
		 *    property="subtitle",
		 *    type="string",
		 *   ),
		 *   @OA\Property (
		 *    property="description",
		 *    type="string",
		 *   ),
		 *   @OA\Property (
		 *    property="buttons",
		 *    type="array",
		 *    @OA\Items (
		 *     type="object",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="fullPage",
		 *    type="boolean",
		 *   ),
		 *   @OA\Property (
		 *    property="_placeholders",
		 *    type="object",
		 *    @OA\Property (
		 *     property="subtitle",
		 *     type="string",
		 *    ),
		 *    @OA\Property (
		 *     property="description",
		 *     type="string",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="_toggle",
		 *    type="object",
		 *    @OA\Property (
		 *     property="title",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="subtitle",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="description",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="buttons",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="global.logo",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="global.accounts",
		 *     type="boolean",
		 *    ),
		 *    @OA\Property (
		 *     property="global.title",
		 *     type="boolean",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="arrow",
		 *    type="boolean",
		 *   ),
		 *   @OA\Property (
		 *    property="fixedNavigation",
		 *    type="boolean",
		 *   ),
		 *   @OA\Property (
		 *    property="fitText",
		 *    type="boolean",
		 *   ),
		 *   @OA\Property (
		 *    property="contentAlignment",
		 *    type="string",
		 *   ),
		 *   @OA\Property (
		 *    property="background",
		 *    type="object",
		 *    @OA\Property (
		 *     property="colorIndex",
		 *     type="integer",
		 *    ),
		 *    @OA\Property (
		 *     property="img",
		 *     type="object",
		 *     @OA\Property (
		 *      property="value",
		 *      type="string",
		 *     ),
		 *    ),
		 *    @OA\Property (
		 *     property="opacity",
		 *     type="integer",
		 *    ),
		 *    @OA\Property (
		 *     property="color",
		 *     type="array",
		 *     @OA\Items (
		 *      type="object",
		 *     ),
		 *    ),
		 *    @OA\Property (
		 *     property="none",
		 *     type="boolean",
		 *    ),
		 *   ),
		 *   @OA\Property (
		 *    property="cover",
		 *    type="boolean",
		 *   ),
		 *   @OA\Property (
		 *    property="companyName",
		 *    type="string",
		 *   ),
		 *  )
		 * )
		 */
		$val = get_option(
			WB4WP_HEADER_SECTION,
			array(
				'id'       => 22,
				'layout'   =>
					array(
						'section' =>
							array(
								'id' => 'bajigu78',
							),
					),
				'category' => 'headers',
				'binding'  =>
					array(
						'title'            => 'Click here to edit your title',
						'subtitle'         => 'Click here to edit your subtitle',
						'description'      => 'Click here to edit your description',
						'buttons'          =>
							array(),
						'fullPage'         => true,
						'_placeholders'    =>
							array(
								'subtitle'    => 'It\'s nice to meet you',
								'description' => 'Write something here to introduce yourself to your audience.',
							),
						'_toggle'          =>
							array(
								'title'           => true,
								'subtitle'        => true,
								'description'     => true,
								'buttons'         => true,
								'global.logo'     => false,
								'global.accounts' => true,
								'global.title'    => true,
							),
						'arrow'            => true,
						'fixedNavigation'  => true,
						'fitText'          => true,
						'contentAlignment' => 'align-center',
						'background'       =>
							array(
								'colorIndex' => 0,
								'img'        =>
									array(
										'value' => 'https://images.builderservices.io/s/?https://images.unsplash.com/photo-1493836512294-502baa1986e2?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjU1MTN9',
									),
								'opacity'    => 50,
								'color'      =>
									array(),
								'none'       => true,
							),
						'cover'            => true,
						'companyName'      => 'roberts pstcologist work',
					),
			)
		);

		if ( is_string( $val ) ) {
			$val = json_decode( $val, true );
		}

		return $val;
	}

	public static function get_option_as_array( $key ) {
		$val = get_option( $key, array() );

		if ( is_string( $val ) ) {
			return json_decode( $val, true );
		}

		return $val;
	}

}
