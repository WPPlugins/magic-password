<?php

namespace TwoFAS\MagicPassword\Controllers;

use TwoFAS\MagicPassword\Helpers\Config;
use TwoFAS\MagicPassword\Helpers\Flash;
use TwoFAS\MagicPassword\Helpers\URL;
use TwoFAS\MagicPassword\Http\JSON_Response;
use TwoFAS\MagicPassword\Http\Redirection_Response;
use TwoFAS\MagicPassword\Http\View_Response;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\MagicPassword\Integration\WP_Integration;
use TwoFAS\MagicPassword\Middleware\Check_Account;
use TwoFAS\MagicPassword\Middleware\Check_Nonce;

abstract class Controller {
	/**
	 * @var Flash
	 */
	protected $flash;

	/**
	 * @var User_Storage
	 */
	protected $user;

	/**
	 * @var WP_Integration
	 */
	protected $wp_integration;

	/**
	 * @var URL
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $session_id;

	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * @param Flash          $flash
	 * @param User_Storage   $user
	 * @param WP_Integration $wp_integration
	 * @param URL            $url
	 * @param string         $session_id
	 * @param Config         $config
	 */
	public function __construct( Flash $flash, User_Storage $user, WP_Integration $wp_integration, URL $url, $session_id, Config $config ) {
		$this->flash          = $flash;
		$this->user           = $user;
		$this->wp_integration = $wp_integration;
		$this->url            = $url;
		$this->session_id     = $session_id;
		$this->config         = $config;
	}

	/**
	 * @param string $template_name
	 * @param array  $data
	 *
	 * @return View_Response
	 */
	public function view( $template_name, array $data = array() ) {
		return new View_Response( $template_name, $data );
	}

	/**
	 * @param string $url
	 * @param array  $data
	 *
	 * @return Redirection_Response
	 */
	public function redirection( $url, array $data = array() ) {
		return new Redirection_Response( $url, $data );
	}

	/**
	 * @param array $body
	 * @param int   $status_code
	 *
	 * @return JSON_Response
	 */
	public function json( array $body, $status_code = 200 ) {
		return new JSON_Response( $body, $status_code );
	}

	/**
	 * @return null|View_Response
	 */
	public function handle_account_check() {
		$middleware = new Check_Account( $this->wp_integration, $this->flash, $this->url );

		return $middleware->handle();
	}

	/**
	 * @return bool
	 */
	public function check_account() {
		$middleware = new Check_Account( $this->wp_integration, $this->flash, $this->url );

		return $middleware->check();
	}

	/**
	 * @param string $nonce
	 * @param string $action
	 *
	 * @return null|Redirection_Response
	 */
	public function handle_nonce_check( $nonce, $action ) {
		$middleware = new Check_Nonce( $this->flash, $this->url );

		return $middleware->handle( $nonce, $action );
	}

	/**
	 * @param string $nonce
	 * @param string $action
	 *
	 * @return bool
	 */
	public function check_nonce( $nonce, $action ) {
		$middleware = new Check_Nonce( $this->flash, $this->url );

		return $middleware->check( $nonce, $action );
	}
}
