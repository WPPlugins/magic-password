<?php

namespace TwoFAS\MagicPassword\Integration;

use TwoFAS\MagicPassword\Helpers\Browser;

abstract class SDK_Factory {
	/**
	 * @param array $app_details
	 *
	 * @return array
	 */
	protected function get_headers( array $app_details ) {
		$browser     = new Browser();
		$app_version = $app_details['version'];
		$app_name    = $app_details['name'];
		$app_url     = $app_details['url'];

		return array(
			'Plugin-Version'  => MPWD_PLUGIN_VERSION,
			'App-Version'     => $app_version,
			'App-Name'        => $app_name,
			'App-Url'         => $app_url,
			'Browser-Version' => $browser->describe(),
			'Php-Version'     => PHP_VERSION,
		);
	}
}
