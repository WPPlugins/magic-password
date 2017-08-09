<?php

namespace TwoFAS\MagicPassword\Integration;

use TwoFAS\UserZone\OAuth\TokenType;
use TwoFAS\UserZone\UserZone;

class User_Zone_Factory extends SDK_Factory {
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
	 * @return UserZone
	 */
	public function create() {
		$app_details = $this->integration_storage->get_app_details();
		$headers     = $this->get_headers( $app_details );

		$user_zone = new UserZone( $this->integration_storage, TokenType::passwordlessWordpress(), $headers );
		$user_zone->setBaseUrl( $this->url );

		return $user_zone;
	}
}
