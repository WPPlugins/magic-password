<?php

namespace TwoFAS\MagicPassword\Integration;

use TwoFAS\Encryption\AESKey;
use TwoFAS\Encryption\Interfaces\Key;
use TwoFAS\Encryption\Interfaces\KeyStorage;
use TwoFAS\MagicPassword\Exceptions\Encryption_Key_Not_Found_Exception;
use TwoFAS\UserZone\OAuth\Interfaces\TokenStorage;
use TwoFAS\UserZone\OAuth\Token;
use TwoFAS\UserZone\OAuth\TokenNotFoundException;
use TwoFAS\UserZone\OAuth\TokenType;

class Integration_Storage implements KeyStorage, TokenStorage {
	const MPWD_ENCRYPTION_KEY        = 'mpwd_encryption_key';
	const MPWD_OAUTH_TOKEN_BASE      = 'mpwd_oauth_token_';
	const ACCESS_TOKEN_KEY           = 'access_token';
	const INTEGRATION_ID_KEY         = 'integration_id';
	const MPWD_INTEGRATION_LOGIN_KEY = 'mpwd_integration_login';
	const MPWD_KEY_TOKEN_KEY         = 'mpwd_key_token';
	const MPWD_EMAIL_KEY             = 'mpwd_email';

	/**
	 * @param Key $key
	 */
	public function storeKey( Key $key ) {
		update_option( self::MPWD_ENCRYPTION_KEY, base64_encode( $key->getValue() ) );
	}

	/**
	 * @return Key
	 *
	 * @throws Encryption_Key_Not_Found_Exception
	 */
	public function retrieveKey() {
		$encryption_key = get_option( self::MPWD_ENCRYPTION_KEY );

		if ( ! $encryption_key ) {
			throw new Encryption_Key_Not_Found_Exception();
		}

		$encryption_key = base64_decode( $encryption_key );

		return new AESKey( $encryption_key );
	}

	/**
	 * @param Token $token
	 */
	public function storeToken( Token $token ) {
		$option_name = $this->create_oauth_token_option_name( $token->getType() );

		update_option( $option_name, array(
			self::ACCESS_TOKEN_KEY   => $token->getAccessToken(),
			self::INTEGRATION_ID_KEY => $token->getIntegrationId(),
		) );
	}

	/**
	 * @param string $type
	 *
	 * @return Token
	 *
	 * @throws TokenNotFoundException
	 */
	public function retrieveToken( $type ) {
		$option_name = $this->create_oauth_token_option_name( $type );
		$token_array = get_option( $option_name );

		if ( is_array( $token_array ) ) {
			return new Token( $type, $token_array[self::ACCESS_TOKEN_KEY], $token_array[self::INTEGRATION_ID_KEY] );
		}

		throw new TokenNotFoundException();
	}

	/**
	 * @return int|null
	 */
	public function retrieve_integration_id() {
		try {
			$token = $this->retrieveToken( TokenType::passwordlessWordpress()->getType() );
		} catch ( TokenNotFoundException $e ) {
			return null;
		}

		return $token->getIntegrationId();
	}

	/**
	 * @param string $login
	 */
	public function store_integration_login( $login ) {
		update_option( self::MPWD_INTEGRATION_LOGIN_KEY, $login );
	}

	/**
	 * @return null|string
	 */
	public function retrieve_integration_login() {
		return get_option( self::MPWD_INTEGRATION_LOGIN_KEY, null );
	}

	/**
	 * @param string $token
	 */
	public function store_key_token( $token ) {
		update_option( self::MPWD_KEY_TOKEN_KEY, $token );
	}

	/**
	 * @return null|string
	 */
	public function retrieve_key_token() {
		return get_option( self::MPWD_KEY_TOKEN_KEY, null );
	}

	/**
	 * @param string $email
	 */
	public function store_email( $email ) {
		update_option( self::MPWD_EMAIL_KEY, $email );
	}

	/**
	 * @return null|string
	 */
	public function retrieve_email() {
		return get_option( self::MPWD_EMAIL_KEY, null );
	}

	/**
	 * @return string
	 */
	public function get_wp_url() {
		return get_bloginfo( 'wpurl' );
	}

	/**
	 * @return string
	 */
	public function get_wp_version() {
		return get_bloginfo( 'version' );
	}

	/**
	 * @return string
	 */
	public function get_wp_name() {
		return get_bloginfo( 'name' );
	}

	/**
	 * @return array
	 */
	public function get_app_details() {
		$app_version = $this->get_wp_version();
		$app_name    = $this->get_wp_name();
		$app_url     = $this->get_wp_url();

		return array(
			'version' => $app_version,
			'name'    => $app_name,
			'url'     => $app_url,
		);
	}

	/**
	 * @param Key    $encryption_key
	 * @param string $integration_login
	 * @param string $key_token
	 * @param string $email
	 */
	public function store_integration( Key $encryption_key, $integration_login, $key_token, $email ) {
		$this->storeKey( $encryption_key );
		$this->store_integration_login( $integration_login );
		$this->store_key_token( $key_token );
		$this->store_email( $email );
	}

	/**
	 * @return bool
	 */
	public function exists() {
		$integration_login = $this->retrieve_integration_login();
		$key_token         = $this->retrieve_key_token();
		$email             = $this->retrieve_email();

		try {
			$this->retrieveKey();
		} catch ( Encryption_Key_Not_Found_Exception $e ) {
			return false;
		}

		try {
			$this->retrieveToken( TokenType::PASSWORDLESS_WORDPRESS );
		} catch ( TokenNotFoundException $e ) {
			return false;
		}

		return ! is_null( $integration_login )
		       && ! is_null( $key_token )
		       && ! is_null( $email );
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	private function create_oauth_token_option_name( $type ) {
		return self::MPWD_OAUTH_TOKEN_BASE . $type;
	}
}
