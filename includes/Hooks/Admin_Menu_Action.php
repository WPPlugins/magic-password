<?php

namespace TwoFAS\MagicPassword\Hooks;

use TwoFAS\MagicPassword\Helpers\Twig;
use TwoFAS\MagicPassword\Http\View_Response;

class Admin_Menu_Action {
	/**
	 * @var Twig
	 */
	private $twig;

	/**
	 * @var View_Response
	 */
	private $response;

	/**
	 * @param Twig          $twig
	 * @param View_Response $response
	 */
	public function __construct( Twig $twig, View_Response $response ) {
		$this->twig     = $twig;
		$this->response = $response;
	}

	public function add_pages() {
		add_menu_page( 'Magic Password - Settings', 'Magic Password', 'read', 'magic-password-settings', array(
			$this,
			'render'
		) );
	}

	public function render() {
		echo $this->twig->render( $this->response->get_template_name(), $this->response->get_data() );
	}
}
