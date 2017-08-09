<?php

namespace TwoFAS\MagicPassword\Core;

use TwoFAS\MagicPassword\Helpers\Config;
use TwoFAS\MagicPassword\Helpers\Flash;
use TwoFAS\MagicPassword\Helpers\Hash;
use TwoFAS\MagicPassword\Helpers\Twig;
use TwoFAS\MagicPassword\Helpers\URL;
use TwoFAS\MagicPassword\Hooks\Action_Links_Filter;
use TwoFAS\MagicPassword\Hooks\Admin_Menu_Action;
use TwoFAS\MagicPassword\Hooks\Admin_Notices_Action;
use TwoFAS\MagicPassword\Hooks\Authenticate_Action;
use TwoFAS\MagicPassword\Hooks\Enqueue_Scripts_Action;
use TwoFAS\MagicPassword\Hooks\Login_Form_Action;
use TwoFAS\MagicPassword\Http\JSON_Response;
use TwoFAS\MagicPassword\Http\Redirection_Response;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\MagicPassword\Http\Route;
use TwoFAS\MagicPassword\Http\View_Response;
use TwoFAS\MagicPassword\Integration\API_Factory;
use TwoFAS\MagicPassword\Integration\Integration_Storage;
use TwoFAS\MagicPassword\Integration\Registration_Service;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\MagicPassword\Integration\User_Zone_Factory;
use TwoFAS\MagicPassword\Integration\WP_Integration;
use TwoFAS\MagicPassword\Update\Updater;

class Plugin {
	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var Integration_Storage
	 */
	private $integration_storage;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Route
	 */
	private $route;

	/**
	 * @var Updater
	 */
	private $updater;

	/**
	 * @var string
	 */
	private $session_id;

	public function __construct() {
		$this->config              = new Config();
		$this->integration_storage = new Integration_Storage();
		$this->request             = new Request( $_GET, $_POST, $_SERVER, $_COOKIE );
		$this->flash               = new Flash( $this->request->cookie(), $this->config );
		$this->url                 = new URL();
		$this->twig                = new Twig( $this->flash, $this->url );
		$user_zone_factory         = new User_Zone_Factory( $this->config->get_user_zone_url(), $this->integration_storage );
		$user_zone                 = $user_zone_factory->create();

		if ( ! $this->integration_storage->exists() ) {
			$service = new Registration_Service( $user_zone, $this->integration_storage );
			$service->register();
		}

		$user                 = new User_Storage( get_current_user_id() );
		$api_factory          = new API_Factory( $this->config->get_api_url(), $this->integration_storage );
		$api                  = $api_factory->create();
		$this->wp_integration = new WP_Integration( $this->integration_storage, $api, $user_zone );
		$session_id           = Hash::generate();
		$this->session_id     = $session_id;
		$this->route          = new Route( $this->request, $this->flash, $user, $this->wp_integration, $this->url, $session_id, $this->config );
		$this->updater        = new Updater();
	}

	public function start() {
		$this->updater->update();

		$response = $this->route->get_response();

		if ( $response instanceof JSON_Response ) {
			$response->send_json();
		}

		if ( $response instanceof Redirection_Response ) {
			$response->redirect();
		}

		if ( $response instanceof View_Response ) {
			$this->add_hooks( $response );
		}
	}

	/**
	 * @param View_Response $response
	 */
	private function add_hooks( View_Response $response ) {
		$admin_notices_action   = new Admin_Notices_Action( $this->twig );
		$login_form_action      = new Login_Form_Action( $this->request, $this->integration_storage, $this->twig, $this->session_id, $this->config );
		$admin_menu_action      = new Admin_Menu_Action( $this->twig, $response );
		$enqueue_scripts_action = new Enqueue_Scripts_Action( $this->config );
		$authenticate_action    = new Authenticate_Action( $this->request, $this->wp_integration, $this->config );
		$action_links_filter    = new Action_Links_Filter( $this->url );

		add_action( 'login_form', array( $login_form_action, 'customize_login_page' ) );
		add_action( 'login_enqueue_scripts', array( $enqueue_scripts_action, 'enqueue_login' ) );
		add_action( 'authenticate', array( $authenticate_action, 'authenticate' ), 99 );
		add_action( 'admin_menu', array( $admin_menu_action, 'add_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $enqueue_scripts_action, 'enqueue_dashboard' ) );
		add_action( 'admin_notices', array( $admin_notices_action, 'render_notices' ) );
		add_filter( 'plugin_action_links_' . MPWD_PLUGIN_BASENAME, array(
			$action_links_filter,
			'add_settings_link'
		) );
	}
}
