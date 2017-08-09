<?php

namespace TwoFAS\MagicPassword\Middleware;

use TwoFAS\MagicPassword\Helpers\Flash;
use TwoFAS\MagicPassword\Helpers\URL;
use TwoFAS\MagicPassword\Http\View_Response;
use TwoFAS\MagicPassword\Integration\WP_Integration;

class Check_Account {
	/**
	 * @var WP_Integration
	 */
	private $wp_integration;

	/**
	 * @var Flash
	 */
	private $flash;

	/**
	 * @var URL
	 */
	private $url;

	/**
	 * @param WP_Integration $wp_integration
	 * @param Flash          $flash
	 * @param URL            $url
	 */
	public function __construct( WP_Integration $wp_integration, Flash $flash, URL $url ) {
		$this->wp_integration = $wp_integration;
		$this->flash          = $flash;
		$this->url            = $url;
	}

	/**
	 * @return null|View_Response
	 */
	public function handle() {
		if ( $this->check() ) {
			return null;
		}

		return new View_Response( 'dashboard/error.html.twig', array(
			'description' => 'Account has not been created yet.',
		) );
	}

	/**
	 * @return bool
	 */
	public function check() {
		return $this->wp_integration->is_account_created();
	}
}
