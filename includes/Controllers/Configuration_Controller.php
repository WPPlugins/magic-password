<?php

namespace TwoFAS\MagicPassword\Controllers;

use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\MagicPassword\Exceptions\Failed_Pairing_Exception;
use TwoFAS\MagicPassword\Helpers\Config;
use TwoFAS\MagicPassword\Helpers\Flash;
use TwoFAS\MagicPassword\Helpers\URL;
use TwoFAS\MagicPassword\Http\JSON_Response;
use TwoFAS\MagicPassword\Http\Redirection_Response;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\MagicPassword\Http\View_Response;
use TwoFAS\MagicPassword\Integration\Pair_Service;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\MagicPassword\Integration\WP_Integration;

class Configuration_Controller extends Controller {
	/**
	 * @var Pair_Service
	 */
	private $pair_service;

	/**
	 * @param Flash          $flash
	 * @param User_Storage   $user
	 * @param WP_Integration $wp_integration
	 * @param URL            $url
	 * @param string         $session_id
	 * @param Config         $config
	 */
	public function __construct( Flash $flash, User_Storage $user, WP_Integration $wp_integration, URL $url, $session_id, Config $config ) {
		parent::__construct( $flash, $user, $wp_integration, $url, $session_id, $config );
		$this->pair_service = new Pair_Service( $this->wp_integration, $this->user );
	}

	/**
	 * @param Request $request
	 *
	 * @return JSON_Response
	 */
	public function pair( Request $request ) {
		$result = $this->middleware_pair( $request );

		if ( $result ) {
			return $result;
		}

		$auth_id      = $request->post( 'auth_id' );
		$totp_secret  = $request->post( 'totp_secret' );
		$totp_code    = $request->post( 'totp_code' );
		$channel_name = $request->post( 'channel_name' );
		$status_id    = intval( $request->post( 'status_id' ) );

		try {
			$this->pair_service->pair( $auth_id, $totp_secret, $totp_code, $channel_name, $status_id );

			return $this->json( array() );
		} catch ( Failed_Pairing_Exception $e ) {
			$body = array(
				'error' => $e->getMessage(),
			);

			return $this->json( $body, $e->getCode() );
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return Redirection_Response|View_Response
	 *
	 * @throws Api_Exception
	 */
	public function unpair( Request $request ) {
		$result = $this->middleware_unpair( $request );

		if ( $result ) {
			return $result;
		}

		$url              = $this->url->make();
		$integration_user = $this->wp_integration->get_integration_user_by_external_id( $this->user->get_id() );

		if ( is_null( $integration_user ) ) {
			$this->flash->add_message( 'error', 'Integration user not found.' );

			return $this->redirection( $url );
		}

		$integration_user->setTotpSecret( null );
		$this->wp_integration->update_integration_user( $integration_user );
		$this->user->delete_configuration();

		$cookie = $request->cookie();
		$cookie->delete_cookie( $this->config->get_login_cookie_name() );

		$this->flash->add_message( 'success', 'Configuration has been removed successfully.' );

		return $this->redirection( $url );
	}

	/**
	 * @param Request $request
	 *
	 * @return JSON_Response|null
	 */
	private function middleware_pair( Request $request ) {
		if ( ! $this->check_account() ) {
			return $this->json( array(
				'error' => 'Account has not been created yet.',
			), 403 );
		}

		$nonce  = $request->post( '_wpnonce' );
		$action = 'pair';

		if ( ! $this->check_nonce( $nonce, $action ) ) {
			return $this->json( array(
				'error' => 'Invalid nonce',
			), 403 );
		}

		return null;
	}

	/**
	 * @param Request $request
	 *
	 * @return null|Redirection_Response|View_Response
	 */
	private function middleware_unpair( Request $request ) {
		$result = $this->handle_account_check();

		if ( $result ) {
			return $result;
		}

		$nonce  = $request->get( '_wpnonce' );
		$action = 'unpair';

		$result = $this->handle_nonce_check( $nonce, $action );

		if ( $result ) {
			return $result;
		}

		return null;
	}
}
