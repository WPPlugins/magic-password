<?php

namespace TwoFAS\MagicPassword\Hooks;

use TwoFAS\MagicPassword\Helpers\Config;

class Enqueue_Scripts_Action {
	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	public function enqueue_login() {
		$this->enqueue_common();
		wp_enqueue_script( 'mpwd-login', MPWD_ASSETS_URL . 'js/login.js', array( 'jquery' ), MPWD_PLUGIN_VERSION, true );
	}

	public function enqueue_dashboard() {
		wp_enqueue_style( 'poppins', 'https://fonts.googleapis.com/css?family=Poppins:300,400,500,700', array(), MPWD_PLUGIN_VERSION );
		$this->enqueue_common();
		wp_enqueue_script( 'mpwd-dashboard', MPWD_ASSETS_URL . 'js/dashboard.js', array( 'jquery' ), MPWD_PLUGIN_VERSION, true );
	}

	private function enqueue_common() {
		wp_enqueue_style( 'magic-password', MPWD_ASSETS_URL . 'css/magic-password.css', array(), MPWD_PLUGIN_VERSION );
		wp_enqueue_script( 'mpwd-modals', MPWD_ASSETS_URL . 'js/modals.js', array( 'jquery' ), MPWD_PLUGIN_VERSION, true );
		$this->enqueue_pusher();
	}

	private function enqueue_pusher() {
		wp_enqueue_script( 'pusher', '//js.pusher.com/4.0/pusher.min.js', array( 'jquery' ), '4.0.0', true );
		wp_enqueue_script( 'mpwd-pusher-events', MPWD_ASSETS_URL . 'js/pusher-events.js', array( 'jquery' ), MPWD_PLUGIN_VERSION, true );

		$baseUrl = admin_url( 'admin.php?page=magic-password-settings&mpwd-action=' );

		$data = array(
			'pusherKey'            => $this->config->get_pusher_key(),
			'authenticateEndpoint' => $baseUrl . 'authenticate-channel',
			'pairEndpoint'         => $baseUrl . 'pair',
		);

		wp_localize_script( 'mpwd-pusher-events', 'mpwd', $data );
	}
}
