<?php

namespace TwoFAS\MagicPassword\Controllers;

use TwoFAS\MagicPassword\Http\Redirection_Response;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\MagicPassword\Http\View_Response;

class Settings_Update_Controller extends Controller {
	/**
	 * @param Request $request
	 *
	 * @return Redirection_Response|View_Response
	 */
	public function enable_plugin( Request $request ) {
		$nonce  = $request->get( '_wpnonce' );
		$action = 'enable-plugin';
		$result = $this->middleware( $nonce, $action );

		if ( $result ) {
			return $result;
		}

		$this->user->enable_mpwd();
		$this->flash->add_message( 'success', 'Magic Password has been enabled.' );

		$url = $this->url->make();

		return $this->redirection( $url );
	}

	/**
	 * @param Request $request
	 *
	 * @return Redirection_Response|View_Response
	 */
	public function disable_plugin( Request $request ) {
		$nonce  = $request->get( '_wpnonce' );
		$action = 'disable-plugin';
		$result = $this->middleware( $nonce, $action );

		if ( $result ) {
			return $result;
		}

		$this->user->disable_mpwd();

		$cookie = $request->cookie();
		$cookie->delete_cookie( $this->config->get_login_cookie_name() );

		$this->flash->add_message( 'success', 'Magic Password has been disabled.' );

		$url = $this->url->make();

		return $this->redirection( $url );
	}

	/**
	 * @param Request $request
	 *
	 * @return Redirection_Response|View_Response
	 */
	public function update_settings( Request $request ) {
		$nonce  = $request->post( '_wpnonce' );
		$action = 'update-settings';
		$result = $this->middleware( $nonce, $action );

		if ( $result ) {
			return $result;
		}

		$value = $request->post( 'mf-only' );

		if ( 'yes' === $value ) {
			$this->user->make_mpwd_only_auth_option();
			$this->flash->add_message( 'success', 'Magic Password is the only authentication option from now.' );
		} else {
			$this->user->make_mpwd_second_auth_option();
			$this->flash->add_message( 'success', 'You can log in with Magic Password and your WordPress login and password.' );
		}

		$url = $this->url->make();

		return $this->redirection( $url );
	}

	/**
	 * @param string $nonce
	 * @param string $action
	 *
	 * @return null|Redirection_Response|View_Response
	 */
	private function middleware( $nonce, $action ) {
		$result = $this->handle_account_check();

		if ( $result ) {
			return $result;
		}

		$result = $this->handle_nonce_check( $nonce, $action );

		if ( $result ) {
			return $result;
		}

		return null;
	}
}
