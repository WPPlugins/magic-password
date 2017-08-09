<?php

namespace TwoFAS\MagicPassword\Helpers;

use Twig_Environment;
use Twig_Loader_Filesystem;

class Twig {
	/**
	 * @var Twig_Environment
	 */
	private $twig;

	/**
	 * @param Flash $flash
	 * @param URL   $url
	 */
	public function __construct( Flash $flash, URL $url ) {
		$loader     = new Twig_Loader_Filesystem( MPWD_TEMPLATES_PATH );
		$this->twig = new Twig_Environment( $loader );
		$this->twig->addGlobal( 'flash', $flash );
		$this->twig->addGlobal( 'url', $url );
	}

	/**
	 * @param string $template_name
	 * @param array  $data
	 *
	 * @return string
	 */
	public function render( $template_name, array $data = array() ) {
		$data['assets_url'] = MPWD_ASSETS_URL;

		return $this->twig->render( $template_name, $data );
	}
}
