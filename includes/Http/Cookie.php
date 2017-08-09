<?php

namespace TwoFAS\MagicPassword\Http;

class Cookie {
	/**
	 * @var array
	 */
	private $cookies;

	/**
	 * @param array $cookies
	 */
	public function __construct( array $cookies ) {
		$this->cookies = $cookies;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has_cookie( $name ) {
		return isset( $this->cookies[ $name ] );
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param int    $lifespan Time in seconds the cookie exists.
	 */
	public function set_cookie( $name, $value, $lifespan ) {
		$expire = time() + $lifespan;
		setcookie( $name, $value, $expire, '/' );
	}

	/**
	 * @param string $name
	 *
	 * @return array|string
	 */
	public function get_cookie( $name ) {
		if ( $this->has_cookie( $name ) ) {
			return $this->cookies[ $name ];
		}

		return '';
	}

	/**
	 * @param string $name
	 */
	public function delete_cookie( $name ) {
		$this->set_cookie( $name, '', -3600 );

		if ( $this->has_cookie( $name ) ) {
			unset( $_COOKIE[ $name ] );
		}
	}
}
