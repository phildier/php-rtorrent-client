<?php

namespace PHPRtorrentClient;

class Response {

	private $request = null;
	private $response = null;
	private $is_error = false;
	private $error_code = null;
	private $error_string = null;

	public function __construct(Request $request, \GuzzleHttp\Psr7\Response $response) {
		$this->request = $request;
		$this->response = xmlrpc_decode($response->getBody());

		if(isset($this->response->faultcode)) {
			$this->is_error = true;
			$this->error_code = $this->response->faultCode;
			$this->error_string = $this->response->faultString;
		}
	}

	public function __toString() {
		return print_r($this->getLast(),true);
	}

	public function getAll() {
		if($this->is_error) {
			return false;
		}

		$ret = array();
		$methods = $this->request->getMethods();

		foreach($methods as $k => $v) {
			if(count($methods) == 1) {
				$ret[$v->getMethod()] = $this->response;
			} else {
				$ret[$v->getMethod()] = $this->response[$k];
			}
		}
		return $ret;
	}

	public function getMethod($method_name) {
		if($this->is_error) {
			return false;
		}

		$all = $this->getAll();
		return $all[$method_name];
	}

	public function getLast() {
		if($this->is_error) {
			return false;
		}

		$all = $this->getAll();
		return array_pop($all);
	}

	public function isError() {
		return $this->is_error;
	}

	public function getErrorCode() {
		return $this->error_code;
	}

	public function getErrorString() {
		return $this->error_string;
	}
}
