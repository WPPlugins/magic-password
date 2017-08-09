<?php

namespace TwoFAS\MagicPassword\Codes;

use TwoFAS\Api\QrCodeGenerator;

class QR_Code_Generator {
	/**
	 * @var QrCodeGenerator
	 */
	private $qr_code_generator;

	/**
	 * @param QrCodeGenerator $qr_code_generator
	 */
	public function __construct( QrCodeGenerator $qr_code_generator ) {
		$this->qr_code_generator = $qr_code_generator;
	}

	/**
	 * @param string $integration_id
	 * @param string $session_id
	 * @param string $totp_secret
	 * @param string $mobile_secret
	 *
	 * @return string
	 */
	public function generate_config_code( $integration_id, $session_id, $totp_secret, $mobile_secret ) {
		$message = "twofas_c://private-wp_{$integration_id}_{$session_id}?s={$totp_secret}&m={$mobile_secret}";

		return $this->qr_code_generator->generateBase64( $message );
	}

	/**
	 * @param string $integration_id
	 * @param string $session_id
	 *
	 * @return string
	 */
	public function generate_login_code( $integration_id, $session_id ) {
		$message = "twofas_l://private-wp_{$integration_id}_{$session_id}";

		return $this->qr_code_generator->generateBase64( $message );
	}
}
