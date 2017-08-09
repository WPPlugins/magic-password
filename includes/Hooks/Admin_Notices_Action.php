<?php

namespace TwoFAS\MagicPassword\Hooks;

use TwoFAS\MagicPassword\Helpers\Twig;

class Admin_Notices_Action {
	/**
	 * @var Twig
	 */
	private $twig;

	/**
	 * @param Twig $twig
	 */
	public function __construct( Twig $twig ) {
		$this->twig = $twig;
	}

	public function render_notices() {
		echo $this->twig->render( 'dashboard/notices.html.twig' );
	}
}
