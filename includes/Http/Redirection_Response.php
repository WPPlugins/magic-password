<?php

namespace TwoFAS\MagicPassword\Http;

class Redirection_Response {
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @param string $url
	 */
	public function __construct( $url ) {
		$this->url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );

	}

	public function redirect() {
		header( "Location: {$this->url}" );
		exit;
	}
}
