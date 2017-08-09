<?php

namespace TwoFAS\MagicPassword\Hooks;

use TwoFAS\MagicPassword\Helpers\URL;

class Action_Links_Filter {
	/**
	 * @var URL
	 */
	private $url;

	/**
	 * @param URL $url
	 */
	public function __construct( URL $url ) {
		$this->url = $url;
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function add_settings_link( array $links ) {
		$url  = $this->url->make();
		$link = "<a href='{$url}'>Settings</a>";

		array_unshift( $links, $link );

		return $links;
	}
}
