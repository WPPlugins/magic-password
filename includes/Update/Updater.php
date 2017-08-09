<?php

namespace TwoFAS\MagicPassword\Update;

class Updater {
	const MPWD_VERSION_KEY = 'mpwd_version';

	public function update() {
		$db_plugin_version = get_option( self::MPWD_VERSION_KEY );

		if ( ! $db_plugin_version || version_compare( $db_plugin_version, MPWD_PLUGIN_VERSION, '<' ) ) {
			update_option( self::MPWD_VERSION_KEY, MPWD_PLUGIN_VERSION );
		}
	}
}
