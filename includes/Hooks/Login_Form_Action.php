<?php

namespace TwoFAS\MagicPassword\Hooks;

use TwoFAS\Api\QrCode\QrClientFactory;
use TwoFAS\Api\QrCodeGenerator;
use TwoFAS\MagicPassword\Codes\QR_Code_Generator;
use TwoFAS\MagicPassword\Helpers\Config;
use TwoFAS\MagicPassword\Helpers\Twig;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\MagicPassword\Integration\Integration_Storage;

class Login_Form_Action {
	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Integration_Storage
	 */
	private $integration_storage;

	/**
	 * @var Twig
	 */
	private $twig;

	/**
	 * @var string
	 */
	private $session_id;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @param Request             $request
	 * @param Integration_Storage $integration_storage
	 * @param Twig                $twig
	 * @param string              $session_id
	 * @param Config              $config
	 */
	public function __construct( Request $request, Integration_Storage $integration_storage, Twig $twig, $session_id, Config $config ) {
		$this->request             = $request;
		$this->integration_storage = $integration_storage;
		$this->twig                = $twig;
		$this->generator           = new QR_Code_Generator( new QrCodeGenerator( QrClientFactory::getInstance() ) );
		$this->session_id          = $session_id;
		$this->config              = $config;
	}

	public function customize_login_page() {
		$integration_id = $this->integration_storage->retrieve_integration_id();
		$qr_code        = $this->generator->generate_login_code( $integration_id, $this->session_id );
		$cookie         = $this->request->cookie();
		$login_cookie   = $cookie->get_cookie( $this->config->get_login_cookie_name() );

		if ( ! $login_cookie ) {
			$login_cookie = '0';
		}

		echo $this->twig->render( 'login.html.twig', array(
			'session_id'     => $this->session_id,
			'integration_id' => $integration_id,
			'qr_code'        => $qr_code,
			'login_cookie'   => $login_cookie,
		) );
	}
}
