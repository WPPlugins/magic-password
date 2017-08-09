<?php

namespace TwoFAS\MagicPassword\Helpers;

class Hash {
	/**
	 * @return string
	 */
	public static function generate() {
		return sha1( uniqid( '', true ) );
	}
}
