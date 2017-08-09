<?php

namespace TwoFAS\MagicPassword\Http;

class View_Response {
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param string $name
	 * @param array  $data
	 */
	public function __construct( $name, array $data = array() ) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function get_template_name() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}
}
