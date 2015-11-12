<?php

namespace PHPRtorrentClient;

class Method
{
	private $method = [];
	private $params = [];

	public function __construct($method, $params = [])
	{
		$this->method = $method;
		$this->params = $params;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getParams()
	{
		return $this->params;
	}
}