<?php

namespace TwoFAS\MagicPassword\Integration;

use TwoFAS\Api\TwoFAS;

class API_Factory extends SDK_Factory {
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var Integration_Storage
	 */
	private $integration_storage;

	/**
	 * @param string              $url
	 * @param Integration_Storage $integration_storage
	 */
	public function __construct( $url, Integration_Storage $integration_storage ) {
		$this->url                 = $url;
		$this->integration_storage = $integration_storage;
	}

	/**
	 * @return TwoFAS
	 */
	public function create() {
		$login       = $this->integration_storage->retrieve_integration_login();
		$key         = $this->integration_storage->retrieve_key_token();
		$app_details = $this->integration_storage->get_app_details();
		$headers     = $this->get_headers( $app_details );

		$api = new TwoFAS( $login, $key, $headers );
		$api->setBaseUrl( $this->url );

		return $api;
	}
}
