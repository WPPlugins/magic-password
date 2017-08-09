<?php

namespace TwoFAS\MagicPassword\Helpers;

class Config {
	/**
	 * @var array
	 */
	private $config;

	public function __construct() {
		$this->config = require MPWD_PLUGIN_PATH . 'config.php';
	}

	/**
	 * @return string
	 */
	public function get_api_url() {
		return $this->config['api_url'];
	}

	/**
	 * @return string
	 */
	public function get_user_zone_url() {
		return $this->config['user_zone_url'];
	}

	/**
	 * @return string
	 */
	public function get_pusher_key() {
		return $this->config['pusher_key'];
	}

	/**
	 * @return string
	 */
	public function get_flash_message_cookie_name_base() {
		return $this->config['flash_message_cookie_name_base'];
	}

	/**
	 * @return int
	 */
	public function get_flash_message_cookie_lifespan() {
		return $this->config['flash_message_cookie_lifespan'];
	}

	/**
	 * @return string
	 */
	public function get_login_cookie_name() {
		return $this->config['login_cookie_name'];
	}

	/**
	 * @return int
	 */
	public function get_login_cookie_name_lifespan() {
		return $this->config['login_cookie_lifespan'];
	}
}
