<?php

namespace TwoFAS\MagicPassword\Integration;

use TwoFAS\Encryption\AESGeneratedKey;
use TwoFAS\MagicPassword\Helpers\Email;
use TwoFAS\MagicPassword\Helpers\Hash;
use TwoFAS\UserZone\Client;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;
use TwoFAS\UserZone\Integration;
use TwoFAS\UserZone\UserZone;

class Registration_Service {
	/**
	 * @var UserZone
	 */
	private $user_zone;

	/**
	 * @var Integration_Storage
	 */
	private $integration_storage;

	/**
	 * @param UserZone            $user_zone
	 * @param Integration_Storage $integration_storage
	 */
	public function __construct( UserZone $user_zone, Integration_Storage $integration_storage ) {
		$this->user_zone           = $user_zone;
		$this->integration_storage = $integration_storage;
	}

	public function register() {
		$email    = new Email();
		$email    = $email->generate();
		$password = Hash::generate();

		$this->create_client( $email, $password );
		$this->create_integration( $email, $password );
	}

	/**
	 * @param string $email
	 * @param string $password
	 *
	 * @return Client|null
	 */
	private function create_client( $email, $password ) {
		try {
			$client = $this->user_zone->createClient( $email, $password, $password, 'wordpress' );

			return $client;
		} catch ( User_Zone_Exception $e ) {
			return null;
		}
	}

	/**
	 * @param string $email
	 * @param string $password
	 *
	 * @return Integration|null
	 */
	private function create_integration( $email, $password ) {
		try {
			$this->user_zone->generateOAuthSetupToken( $email, $password );
			$integration = $this->user_zone->createIntegration( $this->integration_storage->get_wp_name() );
			$this->user_zone->generateIntegrationSpecificToken( $email, $password, $integration->getId() );
			$key = $this->user_zone->createKey( $integration->getId(), 'WordPress' );

			$this->integration_storage->store_integration( new AESGeneratedKey(), $integration->getLogin(), $key->getToken(), $email );

			return $integration;
		} catch ( User_Zone_Exception $e ) {
			return null;
		}
	}
}
