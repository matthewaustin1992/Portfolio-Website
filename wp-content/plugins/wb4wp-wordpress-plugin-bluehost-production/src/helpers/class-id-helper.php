<?php

namespace Wb4Wp\Helpers;

/**
 * Class Id_Helper
 * @package Wb4Wp\Helpers
 */
class Id_Helper {

	private static $id_counter;

	public static function initialize() {
		self::$id_counter = (int) date_timestamp_get( date_create() );
	}

	public static function get_next_id() {
		return self::$id_counter++;
	}

}

Id_Helper::initialize();
