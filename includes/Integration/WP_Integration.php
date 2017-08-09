<?php

namespace TwoFAS\MagicPassword\Integration;

use TwoFAS\Api\Authentication;
use TwoFAS\Api\AuthenticationCollection;
use TwoFAS\Api\Code\Code;
use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\Api\Exception\IntegrationUserNotFoundException;
use TwoFAS\Api\IntegrationUser;
use TwoFAS\Api\MobileSecretGenerator;
use TwoFAS\Api\TwoFAS;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;
use TwoFAS\UserZone\Exception\NotFoundException;
use TwoFAS\UserZone\Integration;
use TwoFAS\UserZone\OAuth\TokenType;
use TwoFAS\UserZone\UserZone;

class WP_Integration {
	/**
	 * @var Integration_Storage
	 */
	private $integration_storage;

	/**
	 * @var TwoFAS
	 */
	private $api;

	/**
	 * @var UserZone
	 */
	private $user_zone;

	/**
	 * @var string
	 */
	private $last_error;

	/**
	 * @param Integration_Storage $integration_storage
	 * @param TwoFAS              $api
	 * @param UserZone            $user_zone
	 */
	public function __construct( Integration_Storage $integration_storage, TwoFAS $api, UserZone $user_zone ) {
		$this->integration_storage = $integration_storage;
		$this->api                 = $api;
		$this->user_zone           = $user_zone;
		$this->last_error          = '';
	}

	/**
	 * @return string
	 */
	public function get_last_error() {
		return $this->last_error;
	}

	/**
	 * @param int $user_id
	 *
	 * @return IntegrationUser
	 *
	 * @throws Api_Exception
	 */
	public function create_integration_user( $user_id ) {
		$integration_user = new IntegrationUser();
		$integration_user->setExternalId( $user_id );
		$integration_user->setMobileSecret( MobileSecretGenerator::generate() );

		$this->api->addIntegrationUser( $this->integration_storage, $integration_user );

		return $integration_user;
	}

	/**
	 * @return bool
	 */
	public function is_account_created() {
		return $this->integration_storage->exists();
	}

	/**
	 * @return Integration|null
	 *
	 * @throws User_Zone_Exception
	 */
	public function get_integration() {
		$token          = $this->integration_storage->retrieveToken( TokenType::PASSWORDLESS_WORDPRESS );
		$integration_id = $token->getIntegrationId();

		try {
			return $this->user_zone->getIntegration( $integration_id );
		} catch ( NotFoundException $e ) {
			return null;
		}
	}

	/**
	 * @param int $user_id
	 *
	 * @return IntegrationUser|null
	 *
	 * @throws Api_Exception
	 */
	public function get_integration_user( $user_id ) {
		try {
			return $this->api->getIntegrationUser( $this->integration_storage, $user_id );
		} catch ( IntegrationUserNotFoundException $e ) {
			return null;
		}
	}

	/**
	 * @param int $user_id
	 *
	 * @return IntegrationUser|null
	 *
	 * @throws Api_Exception
	 */
	public function get_integration_user_by_external_id( $user_id ) {
		try {
			return $this->api->getIntegrationUserByExternalId( $this->integration_storage, $user_id );
		} catch ( IntegrationUserNotFoundException $e ) {
			return null;
		}
	}

	/**
	 * @param IntegrationUser $integration_user
	 *
	 * @return IntegrationUser
	 *
	 * @throws Api_Exception
	 */
	public function update_integration_user( IntegrationUser $integration_user ) {
		return $this->api->updateIntegrationUser( $this->integration_storage, $integration_user );
	}

	/**
	 * @param string $totp_secret
	 *
	 * @return Authentication
	 *
	 * @throws Api_Exception
	 */
	public function request_auth( $totp_secret ) {
		return $this->api->requestAuthViaTotp( $totp_secret );
	}

	/**
	 * @param AuthenticationCollection $authentications
	 * @param string                   $code
	 *
	 * @return Code
	 *
	 * @throws Api_Exception
	 */
	public function check_code( AuthenticationCollection $authentications, $code ) {
		return $this->api->checkCode( $authentications, $code );
	}

	/**
	 * @param string $integration_id
	 * @param string $session_id
	 * @param string $socket_id
	 *
	 * @return array
	 *
	 * @throws Api_Exception
	 */
	public function authenticate_channel( $integration_id, $session_id, $socket_id ) {
		return $this->api->authenticateChannel( $integration_id, $session_id, $socket_id );
	}

	/**
	 * @param string $channel_name
	 * @param int    $status_id
	 * @param string $status
	 *
	 * @return bool
	 */
	public function update_channel_status( $channel_name, $status_id, $status ) {
		try {
			$result = $this->api->updateChannelStatus( $channel_name, $status_id, $status );

			return is_array( $result );
		} catch ( Api_Exception $e ) {
			return false;
		}
	}
}
