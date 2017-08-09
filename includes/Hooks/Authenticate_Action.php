<?php

namespace TwoFAS\MagicPassword\Hooks;

use TwoFAS\Api\Authentication;
use TwoFAS\Api\AuthenticationCollection;
use TwoFAS\Api\ChannelStatuses;
use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\Encryption\Exceptions\RsaDecryptException;
use TwoFAS\Encryption\RsaCryptographer;
use TwoFAS\MagicPassword\Helpers\Config;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\MagicPassword\Integration\WP_Integration;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;
use WP_Error;
use WP_User;

class Authenticate_Action {
	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var WP_Integration
	 */
	private $wp_integration;

	/**
	 * @param Request        $request
	 * @param WP_Integration $wp_integration
	 * @param Config         $config
	 */
	public function __construct( Request $request, WP_Integration $wp_integration, Config $config ) {
		$this->request        = $request;
		$this->wp_integration = $wp_integration;
		$this->config         = $config;
	}

	/**
	 * @param null|WP_Error|WP_User $user
	 *
	 * @return null|WP_Error|WP_User
	 */
	public function authenticate( $user ) {
		$channel_name = $this->request->post( 'channel_name' );
		$status_id    = intval( $this->request->post( 'status_id' ) );

		if ( ! $channel_name ) {
			if ( $user instanceof WP_User ) {
				if ( ! $this->can_log_in( $user->ID ) ) {
					return new WP_Error( 'mpwd_passwordless_only', 'Logging in with login and password is disabled for this account.' );
				}
			}

			return $user;
		}

		$result = $this->process_passwordless_authentication();

		try {
			if ( is_wp_error( $result ) ) {
				$this->wp_integration->update_channel_status( $channel_name, $status_id, ChannelStatuses::REJECTED );
			} else {
				$this->wp_integration->update_channel_status( $channel_name, $status_id, ChannelStatuses::RESOLVED );
			}
		} catch ( Api_Exception $e ) {
			return new WP_Error( 'mpwd_channel_update_error', 'Something went wrong when updating channel.' );
		}

		return $result;
	}

	/**
	 * @param int $user_id
	 *
	 * @return bool
	 */
	private function can_log_in( $user_id ) {
		return ! ( User_Storage::is_mpwd_enabled( $user_id ) && User_Storage::is_mpwd_only_auth_option( $user_id ) );
	}

	/**
	 * @return WP_Error|WP_User
	 */
	private function process_passwordless_authentication() {
		$totp_code           = $this->request->post( 'totp_code' );
		$integration_user_id = $this->request->post( 'integration_user_id' );

		try {
			$integration = $this->wp_integration->get_integration();
		} catch ( User_Zone_Exception $e ) {
			return new WP_Error( 'mpwd_get_integration_error', 'Something went wrong when getting integration.' );
		}

		if ( is_null( $integration ) ) {
			return new WP_Error( 'mpwd_integration_not_found_error', 'Integration not found.' );
		}

		$cryptographer = new RsaCryptographer( $integration->getPublicKey(), $integration->getPrivateKey() );

		try {
			$integration_user_id = $cryptographer->decryptBase64( $integration_user_id );
		} catch ( RsaDecryptException $e ) {
			return new WP_Error( 'mpwd_decrypt_integration_user_id_error', 'Could not decrypt integration user ID.' );
		}

		try {
			$totp_code = $cryptographer->decryptBase64( $totp_code );
		} catch ( RsaDecryptException $e ) {
			return new WP_Error( 'mpwd_decrypt_token_error', 'Could not decrypt TOTP token.' );
		}

		try {
			$integration_user = $this->wp_integration->get_integration_user( $integration_user_id );
		} catch ( Api_Exception $e ) {
			return new WP_Error( 'mpwd_get_integration_user_error', 'Something went wrong when getting integration user.' );
		}

		if ( is_null( $integration_user ) ) {
			return new WP_Error( 'mpwd_integration_user_not_found_error', 'Integration user not found.' );
		}

		if ( ! User_Storage::is_mpwd_enabled( $integration_user->getExternalId() ) ) {
			return new WP_Error( 'mpwd_passwordless_disabled', 'Magic Password is disabled for this account.' );
		}

		if ( is_null( $integration_user->getTotpSecret() ) ) {
			return new WP_Error( 'mpwd_empty_totp_secret_error', 'TOTP secret is missing.' );
		}

		try {
			$auth = $this->wp_integration->request_auth( $integration_user->getTotpSecret() );
		} catch ( Api_Exception $e ) {
			return new WP_Error( 'mpwd_request_auth_error', 'Something went wrong when creating authentication.' );
		}

		$authentications = new AuthenticationCollection();
		$authentications->add( new Authentication( $auth->id(), $auth->createdAt(), $auth->validTo() ) );

		try {
			$code = $this->wp_integration->check_code( $authentications, $totp_code );
		} catch ( Api_Exception $e ) {
			return new WP_Error( 'mpwd_check_code_error', 'Something went wrong when checking token.' );
		}

		if ( ! $code->accepted() ) {
			return new WP_Error( 'mpwd_invalid_token', 'Token is invalid.' );
		}

		$cookie       = $this->request->cookie();
		$cookie_name  = $this->config->get_login_cookie_name();
		$login_cookie = $cookie->get_cookie( $cookie_name );

		if ( ! $login_cookie ) {
			$cookie->set_cookie( $cookie_name, '1', $this->config->get_login_cookie_name_lifespan() );
		}

		return new WP_User( $integration_user->getExternalId() );
	}
}
