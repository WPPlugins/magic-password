<?php

namespace TwoFAS\MagicPassword\Http;

use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\MagicPassword\Helpers\Config;
use TwoFAS\MagicPassword\Helpers\Controller_Factory;
use TwoFAS\MagicPassword\Helpers\Flash;
use TwoFAS\MagicPassword\Helpers\URL;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\MagicPassword\Integration\WP_Integration;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;

class Route {
	/**
	 * @var array
	 */
	private $routes = array(
		'magic-password-settings' => array(
			''                     => array(
				'controller' => 'Settings_Display_Controller',
				'method'     => 'display_settings_page',
			),
			'pair'                 => array(
				'controller' => 'Configuration_Controller',
				'method'     => 'pair',
			),
			'unpair'               => array(
				'controller' => 'Configuration_Controller',
				'method'     => 'unpair',
			),
			'authenticate-channel' => array(
				'controller' => 'Channel_Authentication_Controller',
				'method'     => 'authenticate_channel',
			),
			'enable-plugin'        => array(
				'controller' => 'Settings_Update_Controller',
				'method'     => 'enable_plugin',
			),
			'disable-plugin'       => array(
				'controller' => 'Settings_Update_Controller',
				'method'     => 'disable_plugin',
			),
			'update-settings'      => array(
				'controller' => 'Settings_Update_Controller',
				'method'     => 'update_settings',
			),
		),
	);

	/**
	 * @var JSON_Response|Redirection_Response|View_Response
	 */
	private $response;

	/**
	 * @param Request        $request
	 * @param Flash          $flash
	 * @param User_Storage   $user
	 * @param WP_Integration $wp_integration
	 * @param URL            $url
	 * @param string         $session_id
	 * @param Config         $config
	 */
	public function __construct( Request $request, Flash $flash, User_Storage $user, WP_Integration $wp_integration, URL $url, $session_id, Config $config ) {
		$this->response = $this->create_response( $request, $flash, $user, $wp_integration, $url, $session_id, $config );
	}

	/**
	 * @return JSON_Response|Redirection_Response|View_Response
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * @param string $page
	 * @param string $action
	 *
	 * @return bool
	 */
	private function is_route_valid( $page, $action ) {
		return array_key_exists( $page, $this->routes )
		       && array_key_exists( $action, $this->routes[ $page ] );
	}

	/**
	 * @param string $page
	 * @param string $action
	 *
	 * @return array
	 */
	private function match( $page, $action ) {
		if ( $this->is_route_valid( $page, $action ) ) {
			return array(
				'controller' => $this->routes[ $page ][ $action ]['controller'],
				'method'     => $this->routes[ $page ][ $action ]['method'],
			);
		}

		return array();
	}

	/**
	 * @param Request        $request
	 * @param Flash          $flash
	 * @param User_Storage   $user
	 * @param WP_Integration $wp_integration
	 * @param URL            $url
	 * @param string         $session_id
	 * @param Config         $config
	 *
	 * @return JSON_Response|Redirection_Response|View_Response
	 */
	private function create_response( Request $request, Flash $flash, User_Storage $user, WP_Integration $wp_integration, URL $url, $session_id, Config $config ) {
		$page   = $request->get_page();
		$action = $request->get_action();
		$route  = $this->match( $page, $action );

		if ( empty( $route ) ) {
			return new View_Response( 'dashboard/not-found.html.twig' );
		}

		$controller = Controller_Factory::create( $route['controller'], $flash, $user, $wp_integration, $url, $session_id, $config );
		$method     = $route['method'];

		try {
			return $controller->$method( $request );
		} catch ( Api_Exception $e ) {
			return new View_Response( 'dashboard/error.html.twig', array(
				'description' => 'Exception has been thrown: ' . get_class( $e ),
			) );
		} catch ( User_Zone_Exception $e ) {
			return new View_Response( 'dashboard/error.html.twig', array(
				'description' => 'Exception has been thrown: ' . get_class( $e ),
			) );
		}
	}
}
