<?php

namespace TwoFAS\MagicPassword\Integration;

use DateTime;
use TwoFAS\Api\Authentication;
use TwoFAS\Api\AuthenticationCollection;
use TwoFAS\Api\ChannelStatuses;
use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\Encryption\Exceptions\RsaDecryptException;
use TwoFAS\Encryption\RsaCryptographer;
use TwoFAS\MagicPassword\Exceptions\Failed_Pairing_Exception;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;

class Pair_Service {
	/**
	 * @param WP_Integration $wp_integration
	 * @param User_Storage   $user
	 */
	public function __construct( WP_Integration $wp_integration, User_Storage $user ) {
		$this->wp_integration = $wp_integration;
		$this->user           = $user;
	}

	/**
	 * @param string $auth_id
	 * @param string $totp_secret
	 * @param string $totp_code
	 * @param string $channel_name
	 * @param int    $status_id
	 *
	 * @throws Api_Exception
	 * @throw Pair_Fail_Exception
	 */
	public function pair( $auth_id, $totp_secret, $totp_code, $channel_name, $status_id ) {
		try {
			$this->process_pairing( $auth_id, $totp_code, $totp_secret );

			if ( ! $this->wp_integration->update_channel_status( $channel_name, $status_id, ChannelStatuses::RESOLVED ) ) {
				throw new Failed_Pairing_Exception( 'Something went wrong when updating channel status.', 500 );
			}

			$this->user->enable_mpwd();
			$this->user->make_mpwd_second_auth_option();
		} catch ( Failed_Pairing_Exception $e ) {
			$this->wp_integration->update_channel_status( $channel_name, $status_id, ChannelStatuses::REJECTED );

			throw $e;
		}
	}

	/**
	 * @param string $auth_id
	 * @param string $totp_code
	 * @param string $totp_secret
	 *
	 * @throws Failed_Pairing_Exception
	 */
	private function process_pairing( $auth_id, $totp_code, $totp_secret ) {
		try {
			$integration = $this->wp_integration->get_integration();
		} catch ( User_Zone_Exception $e ) {
			throw new Failed_Pairing_Exception( 'Something went wrong when getting integration.', 500 );
		}

		if ( is_null( $integration ) ) {
			throw new Failed_Pairing_Exception( 'Integration not found.', 404 );
		}

		$cryptographer = new RsaCryptographer( $integration->getPublicKey(), $integration->getPrivateKey() );

		$authentications = new AuthenticationCollection();

		$authentications->add( new Authentication( $auth_id, new DateTime(), new DateTime() ) );

		try {
			$totp_code = $cryptographer->decryptBase64( $totp_code );
		} catch ( RsaDecryptException $e ) {
			throw new Failed_Pairing_Exception( 'Could not decrypt token.', 400 );
		}

		try {
			$code = $this->wp_integration->check_code( $authentications, $totp_code );
		} catch ( Api_Exception $e ) {
			throw new Failed_Pairing_Exception( 'Something went wrong when checking token.', 500 );
		}

		if ( ! $code->accepted() ) {
			throw new Failed_Pairing_Exception( 'Token is invalid.', 400 );
		}

		try {
			$integration_user = $this->wp_integration->get_integration_user_by_external_id( $this->user->get_id() );
		} catch ( Api_Exception $e ) {
			throw new Failed_Pairing_Exception( 'Something went wrong when getting integration user.', 500 );
		}

		if ( is_null( $integration_user ) ) {
			throw new Failed_Pairing_Exception( 'Integration user not found.', 404 );
		}

		$integration_user->setTotpSecret( $totp_secret );

		try {
			$this->wp_integration->update_integration_user( $integration_user );
		} catch ( Api_Exception $e ) {
			throw new Failed_Pairing_Exception( 'Something went wrong when updating integration user.', 500 );
		}
	}
}
