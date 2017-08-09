<?php

namespace TwoFAS\MagicPassword\Helpers;

class Email {
	/**
	 * @return string
	 */
	public function generate() {
		$id = uniqid( '', true );

		return "autocreated+{$id}@magicpassword.io";
	}
}
