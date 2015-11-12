<?php

namespace PHPRtorrentClient;

class Request {

	private $methods = [];

	public function __construct($method = '', $params = [])
	{
		if ($method)
		{
			$this->addMethod($method,$params);
		}
	}

	public function addMethod($method, $params = [])
	{
		$this->methods[] = new Method($method, $params);
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function getMethodCount()
	{
		return count($this->methods);
	}

	public function __toString() {
		if(count($this->methods) == 1) {
			return $this->request();
		} elseif(count($this->methods) > 1) {
			return $this->multi_request();
		}
	}

	private function request() {
		$method = $this->methods[0];
		return $this->encode_request($method->getMethod(),$method->getParams());
	}

	private function multi_request() {
		$multi = array();
		foreach($this->methods as $command) {
			$multi[] = array(
				"methodName" => $command->getMethod(), 
				"params" => $command->getParams()
			);
		}
		return $this->encode_request("system.multicall",array($multi));
	}

	private function encode_request($method,$params) {
		return xmlrpc_encode_request($method,$params);
	}
	
	public function isMultiRequest()
	{
		return ($this->getMethodCount() > 1 ? true : false);
	}
}
