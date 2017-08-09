<?php

namespace TwoFAS\MagicPassword\Controllers;

use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\MagicPassword\Http\JSON_Response;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;

class Channel_Authentication_Controller extends Controller {
	/**
	 * @param Request $request
	 *
	 * @return JSON_Response
	 */
	public function authenticate_channel( Request $request ) {
		$result = $this->middleware();

		if ( $result ) {
			return $result;
		}

		try {
			$integration = $this->wp_integration->get_integration();
		} catch ( User_Zone_Exception $e ) {
			return $this->json( array(
				'error' => 'Something went wrong when getting integration.',
			), 403 );
		}

		if ( is_null( $integration ) ) {
			return $this->json( array(
				'error' => 'Integration not found.',
			), 403 );
		}

		$session_id = $request->get_header( 'HTTP_SESSION_ID' );
		$socket_id  = $request->post( 'socket_id' );

		try {
			$result = $this->wp_integration->authenticate_channel( $integration->getId(), $session_id, $socket_id );

			return $this->json( $result );
		} catch ( Api_Exception $e ) {
			return $this->json( array(
				'error' => 'Something went wrong when authenticating channel.',
			), 403 );
		}
	}

	/**
	 * @return JSON_Response|null
	 */
	private function middleware() {
		if ( ! $this->check_account() ) {
			return $this->json( array(
				'error' => 'Account has not been created yet.',
			), 403 );
		}

		return null;
	}
}
