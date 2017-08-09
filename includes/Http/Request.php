<?php

namespace TwoFAS\MagicPassword\Http;

class Request {
	/**
	 * @var array
	 */
	private $get;

	/**
	 * @var array
	 */
	private $post;

	/**
	 * @var array
	 */
	private $server;

	/**
	 * @var Cookie
	 */
	private $cookie;

	/**
	 * @param array $get
	 * @param array $post
	 * @param array $server
	 * @param array $cookies
	 */
	public function __construct( array $get, array $post, array $server, array $cookies ) {
		$this->get    = $get;
		$this->post   = $post;
		$this->server = $server;
		$this->cookie = new Cookie( $cookies );
	}

	/**
	 * @return string
	 */
	public function get_method() {
		return $this->server['REQUEST_METHOD'];
	}

	/**
	 * @return string
	 */
	public function get_page() {
		$page = $this->get( 'page' );

		return is_string( $page ) ? $page : '';
	}

	/**
	 * @return string
	 */
	public function get_action() {
		$action = $this->get( 'mpwd-action' );
		
		return is_string( $action ) ? $action : '';
	}

	/**
	 * @param string $name
	 *
	 * @return array|string
	 */
	public function get( $name ) {
		if ( isset( $this->get[ $name ] ) ) {
			return $this->get[ $name ];
		}

		return '';
	}

	/**
	 * @param string $name
	 *
	 * @return array|string
	 */
	public function post( $name ) {
		if ( isset( $this->post[ $name ] ) ) {
			return $this->post[ $name ];
		}

		return '';
	}

	/**
	 * @param string $header
	 *
	 * @return string
	 */
	public function get_header( $header ) {
		if ( isset( $this->server[ $header ] ) ) {
			return $this->server[ $header ];
		}

		return '';
	}

	/**
	 * @return Cookie
	 */
	public function cookie() {
		return $this->cookie;
	}
}
