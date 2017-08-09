<?php
/**
 * Plugin Name: Magic Password
 * Plugin URI:  https://magicpassword.io
 * Description: Magic Password is a free security plugin, which allows you to log in by scanning QR code. It’s simple, quick, and highly secure - like magic!
 * Version:     1.0.1
 * Author:      Two Factor Authentication Service Inc.
 * Author URI:  https://2fas.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mpwd_display_wp_version_error() {
	// Plain HTML only
	require_once 'templates/dashboard/requirements/wordpress.html';
}

function mpwd_display_php_version_error() {
	// Plain HTML only
	require_once 'templates/dashboard/requirements/php.html';
}

function mpwd_display_curl_error() {
	// Plain HTML only
	require_once 'templates/dashboard/requirements/curl.html';
}

/**
 * @return bool
 */
function mpwd_check_requirements() {
	require_once 'Magic_Password_Requirements_Verifier.php';
	$verifier = new Magic_Password_Requirements_Verifier();
	$result   = true;

	if ( ! $verifier->check_wp_version() ) {
		$result = false;
		add_action( 'admin_notices', 'mpwd_display_wp_version_error' );
	}

	if ( ! $verifier->check_php_version() ) {
		$result = false;
		add_action( 'admin_notices', 'mpwd_display_php_version_error' );
	}

	if ( ! $verifier->check_curl() ) {
		$result = false;
		add_action( 'admin_notices', 'mpwd_display_curl_error' );
	}

	return $result;
}

function mpwd_define_constants() {
	$plugin_path     = plugin_dir_path( __FILE__ );
	$plugin_url      = plugin_dir_url( __FILE__ );
	$plugin_basename = plugin_basename( __FILE__ );
	$assets_url      = $plugin_url . 'assets/';
	$templates_path  = $plugin_path . 'templates/';

	define( 'MPWD_PLUGIN_PATH',     $plugin_path     );
	define( 'MPWD_PLUGIN_URL',      $plugin_url      );
	define( 'MPWD_PLUGIN_BASENAME', $plugin_basename );
	define( 'MPWD_ASSETS_URL',      $assets_url      );
	define( 'MPWD_TEMPLATES_PATH',  $templates_path  );
	define( 'MPWD_PLUGIN_VERSION',  '1.0.1'          );
}

function mpwd_start() {
	mpwd_define_constants();
	require_once 'start.php';
}

if ( mpwd_check_requirements() ) {
	add_action( 'init', 'mpwd_start' );
}
