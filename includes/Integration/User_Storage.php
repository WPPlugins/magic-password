<?php

namespace TwoFAS\MagicPassword\Integration;

class User_Storage {
	const MPWD_ENABLED_KEY = 'mpwd_enabled';
	const MPWD_ONLY_KEY    = 'mpwd_only_auth_option';

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @param int $id
	 */
	public function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 * @return bool
	 */
	public function is_admin() {
		return $this->has_capability( 'install_plugins' );
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $capability
	 *
	 * @return bool
	 */
	public function has_capability( $capability ) {
		return user_can( $this->id, $capability );
	}

	/**
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public static function is_mpwd_enabled( $user_id ) {
		$enabled = get_user_meta( $user_id, self::MPWD_ENABLED_KEY, true );

		return '1' === $enabled;
	}

	public function enable_mpwd() {
		update_user_meta( $this->id, self::MPWD_ENABLED_KEY, 1 );
	}

	public function disable_mpwd() {
		update_user_meta( $this->id, self::MPWD_ENABLED_KEY, 0 );
	}

	public function make_mpwd_only_auth_option() {
		update_user_meta( $this->id, self::MPWD_ONLY_KEY, 1 );
	}

	public function make_mpwd_second_auth_option() {
		update_user_meta( $this->id, self::MPWD_ONLY_KEY, 0 );
	}

	public function delete_configuration() {
		delete_user_meta( $this->id, self::MPWD_ENABLED_KEY );
		delete_user_meta( $this->id, self::MPWD_ONLY_KEY );
	}

	/**
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public static function is_mpwd_only_auth_option( $user_id ) {
		$passwordless_only = get_user_meta( $user_id, self::MPWD_ONLY_KEY, true );

		return '1' === $passwordless_only;
	}
}
