<?php

namespace Wb4Wp\Integrations\Exceptions;

use Exception;
use Wb4Wp\Helpers\Provider_Helper;

/**
 * Class Theme_Not_Active_Exception
 * @package Wb4Wp\Integrations
 */
class Theme_Not_Active_Exception extends Exception {

	public function __construct( $previous = null ) {
		$provider_name = Provider_Helper::get_provider_name();
		parent::__construct( "The {$provider_name} theme is not active (or is not installed).", 0, $previous );
	}
}
