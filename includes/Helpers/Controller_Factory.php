<?php

namespace TwoFAS\MagicPassword\Helpers;

use TwoFAS\MagicPassword\Controllers\Controller;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\MagicPassword\Integration\WP_Integration;

class Controller_Factory {
	/**
	 * @param string         $controller_name
	 * @param Flash          $flash
	 * @param User_Storage   $user
	 * @param WP_Integration $wp_integration
	 * @param URL            $url
	 * @param string         $session_id
	 * @param Config         $config
	 *
	 * @return Controller
	 */
	public static function create( $controller_name, Flash $flash, User_Storage $user, WP_Integration $wp_integration, URL $url, $session_id, Config $config ) {
		$parts = explode( '_', $controller_name );

		$controller_name = implode( '_', array_map( function( $part ) {
			return ucfirst( $part );
		}, $parts ) );

		$controller_name = self::get_fully_qualified_name( $controller_name );

		return new $controller_name( $flash, $user, $wp_integration, $url, $session_id, $config );
	}

	/**
	 * @param string $controller_name
	 *
	 * @return string
	 */
	private static function get_fully_qualified_name( $controller_name ) {
		return 'TwoFAS\\MagicPassword\\Controllers\\' . $controller_name;
	}
}
