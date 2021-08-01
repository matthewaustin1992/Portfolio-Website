<?php

namespace Wb4Wp\Integrations\Exceptions;

use Exception;
use Wb4Wp\Helpers\Provider_Helper;

/**
 * Class Theme_Does_Not_Support_Setting_Querying_Exception
 * @package Wb4Wp\Integrations
 */
class Theme_Does_Not_Support_Setting_Querying_Exception extends Exception {

	public function __construct( $previous = null ) {
		$provider_name = Provider_Helper::get_provider_name();
		parent::__construct( "The installed {$provider_name} theme version does not support querying of theme settings.", 0, $previous );
	}
}
