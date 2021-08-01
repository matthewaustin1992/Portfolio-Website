<?php

namespace Wb4Wp\Helpers;

/**
 * Class Guid_Helper
 * @package Wb4Wp\Helpers
 */
final class Guid_Helper {

	public static function generate_guid() {
		// phpcs:disable WordPress.WP.AlternativeFunctions.rand_mt_rand
		return sprintf(
			'%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
			mt_rand( 0, 65535 ),
			mt_rand( 0, 65535 ),
			mt_rand( 0, 65535 ),
			mt_rand( 16384, 20479 ),
			mt_rand( 32768, 49151 ),
			mt_rand( 0, 65535 ),
			mt_rand( 0, 65535 ),
			mt_rand( 0, 65535 )
		);
		// phpcs:enable WordPress.WP.AlternativeFunctions.rand_mt_rand
	}
}
