<?php

namespace TwoFAS\MagicPassword\Helpers;

class URL {
	/**
	 * @param string $action
	 *
	 * @return string
	 */
	public function make( $action = '' ) {
		$page = 'magic-password-settings';
		$url  = admin_url( 'admin.php' );
		$url  = add_query_arg( 'page', $page, $url );

		if ( $action ) {
			$url = add_query_arg( 'mpwd-action', $action, $url );
		}

		return $url;
	}

	/**
	 * @param string $action
	 *
	 * @return string
	 */
	public function make_with_nonce( $action = '' ) {
		$url = $this->make( $action );

		return wp_nonce_url( $url, $action );
	}

	/**
	 * @param string $action
	 *
	 * @return string
	 */
	public function make_form_nonce( $action ) {
		return wp_nonce_field( $action, '_wpnonce', true, false );
	}
}
