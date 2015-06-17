<?php

namespace PHPRtorrentClient;

class Method {

	private $method = null;
	private $params = null;

	public function __construct($method,$params=null) {
		$this->method = $method;
		$this->params = $params;
	}

	public function getMethod() {
		return $this->method;
	}

	public function getParams() {
		return $this->params;
	}
}
