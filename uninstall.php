<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'mpwd_version' );
delete_option( 'mpwd_email' );
delete_option( 'mpwd_integration_login' );
delete_option( 'mpwd_key_token' );
delete_option( 'mpwd_encryption_key' );
delete_option( 'mpwd_oauth_token_setup' );
delete_option( 'mpwd_oauth_token_passwordless-wordpress' );

$users = get_users();

foreach ( $users as $user ) {
	delete_user_meta( $user->ID, 'mpwd_enabled' );
	delete_user_meta( $user->ID, 'mpwd_only_auth_option' );
}
