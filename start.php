<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'vendor/autoload.php';

$plugin = new TwoFAS\MagicPassword\Core\Plugin();
$plugin->start();
