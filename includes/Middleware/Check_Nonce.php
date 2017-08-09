<?php

namespace TwoFAS\MagicPassword\Middleware;

use TwoFAS\MagicPassword\Helpers\Flash;
use TwoFAS\MagicPassword\Helpers\URL;
use TwoFAS\MagicPassword\Http\Redirection_Response;

class Check_Nonce {
	/**
	 * @param Flash $flash
	 * @param URL   $url
	 */
	public function __construct( Flash $flash, URL $url ) {
		$this->flash = $flash;
		$this->url   = $url;
	}

	/**
	 * @param string $nonce
	 * @param string $action
	 *
	 * @return null|Redirection_Response
	 */
	public function handle( $nonce, $action ) {
		if ( $this->check( $nonce, $action ) ) {
			return null;
		}

		$this->flash->add_message( 'error', 'Invalid nonce' );

		$url = $this->url->make();

		return new Redirection_Response( $url );
	}

	/**
	 * @param string $nonce
	 * @param string $action
	 *
	 * @return bool
	 */
	public function check( $nonce, $action ) {
		return false !== wp_verify_nonce( $nonce, $action );
	}
}
