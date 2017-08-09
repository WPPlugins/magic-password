<?php

class Magic_Password_Requirements_Verifier {
	const WP_MINIMUM_VERSION  = '4.2';
	const PHP_MINIMUM_VERSION = '5.3.3';

	/**
	 * @return bool
	 */
	public function check_wp_version() {
		$wp_version = get_bloginfo( 'version' );

		return version_compare( $wp_version, self::WP_MINIMUM_VERSION, '>=' );
	}

	/**
	 * @return bool
	 */
	public function check_php_version() {
		return version_compare( PHP_VERSION, self::PHP_MINIMUM_VERSION, '>=' );
	}

	/**
	 * @return bool
	 */
	public function check_curl() {
		return extension_loaded( 'curl' );
	}
}
