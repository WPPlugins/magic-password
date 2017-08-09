<?php

namespace TwoFAS\MagicPassword\Controllers;

use TwoFAS\Api\Exception\Exception as Api_Exception;
use TwoFAS\Api\IntegrationUser;
use TwoFAS\Api\QrCode\QrClientFactory;
use TwoFAS\Api\QrCodeGenerator;
use TwoFAS\Api\TotpSecretGenerator;
use TwoFAS\MagicPassword\Codes\QR_Code_Generator;
use TwoFAS\MagicPassword\Http\Redirection_Response;
use TwoFAS\MagicPassword\Http\Request;
use TwoFAS\MagicPassword\Http\View_Response;
use TwoFAS\MagicPassword\Integration\User_Storage;
use TwoFAS\UserZone\Exception\Exception as User_Zone_Exception;
use TwoFAS\UserZone\Integration;

class Settings_Display_Controller extends Controller {
	/**
	 * @param Request $request
	 *
	 * @return Redirection_Response|View_Response
	 *
	 * @throws Api_Exception
	 * @throws User_Zone_Exception
	 */
	public function display_settings_page( Request $request ) {
		$result = $this->handle_account_check();

		if ( $result ) {
			return $result;
		}

		$integration = $this->wp_integration->get_integration();

		if ( is_null( $integration ) ) {
			$message = 'Integration has not been found but it should exist. ';
			$message .= 'It may be deleted at dashboard.2fas.com. If not, try to refresh this page.';

			return $this->error_view( $message );
		}

		$integration_user = $this->wp_integration->get_integration_user_by_external_id( $this->user->get_id() );

		if ( is_null( $integration_user ) ) {
			$integration_user = $this->wp_integration->create_integration_user( $this->user->get_id() );
		}

		if ( $integration_user->getTotpSecret() ) {
			return $this->configured_view( $integration, $integration_user );
		}

		return $this->not_configured_view( $integration, $integration_user );
	}

	/**
	 * @param string $message
	 *
	 * @return View_Response
	 */
	private function error_view( $message ) {
		return $this->view( 'dashboard/error.html.twig', array(
			'description' => $message,
		) );
	}

	/**
	 * @param Integration     $integration
	 * @param IntegrationUser $integration_user
	 *
	 * @return View_Response
	 *
	 * @throws Api_Exception
	 */
	private function configured_view( Integration $integration, IntegrationUser $integration_user ) {
		$template_name = 'dashboard/settings/configured.html.twig';
		$totp_secret   = $integration_user->getTotpSecret();

		return $this->qr_code_view( $integration, $integration_user, $totp_secret, $template_name );
	}

	/**
	 * @param Integration     $integration
	 * @param IntegrationUser $integration_user
	 *
	 * @return View_Response
	 *
	 * @throws Api_Exception
	 */
	private function not_configured_view( Integration $integration, IntegrationUser $integration_user ) {
		$template_name = 'dashboard/settings/not-configured.html.twig';
		$totp_secret   = TotpSecretGenerator::generate();

		return $this->qr_code_view( $integration, $integration_user, $totp_secret, $template_name );
	}

	/**
	 * @param Integration     $integration
	 * @param IntegrationUser $integration_user
	 * @param string          $totp_secret
	 * @param string          $template_name
	 *
	 * @return View_Response
	 *
	 * @throws Api_Exception
	 */
	private function qr_code_view( Integration $integration, IntegrationUser $integration_user, $totp_secret, $template_name ) {
		$qr_code_generator = new QR_Code_Generator( new QrCodeGenerator( QrClientFactory::getInstance() ) );
		$integration_id    = $integration->getId();
		$mobile_secret     = $integration_user->getMobileSecret();
		$qr_code           = $qr_code_generator->generate_config_code( $integration_id, $this->session_id, $totp_secret, $mobile_secret );
		$authentication    = $this->wp_integration->request_auth( $totp_secret );
		$auth_id           = $authentication->id();
		$configured        = ! is_null( $integration_user->getTotpSecret() );
		$enabled           = User_Storage::is_mpwd_enabled( $integration_user->getExternalId() );
		$passwordless_only = User_Storage::is_mpwd_only_auth_option( $integration_user->getExternalId() );

		return $this->view( $template_name, array(
			'qr_code'                  => $qr_code,
			'session_id'               => $this->session_id,
			'totp_secret'              => $totp_secret,
			'mobile_secret'            => $mobile_secret,
			'auth_id'                  => $auth_id,
			'integration_id'           => $integration_id,
			'is_mpwd_configured'       => $configured,
			'is_plugin_enabled'        => $enabled,
			'is_mpwd_only_auth_option' => $passwordless_only,
		) );
	}
}
