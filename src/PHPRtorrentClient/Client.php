<?php

namespace PHPRtorrentClient;

class Client {

	private $guzzle = null;
	private $rpc_address = null;

	public function __construct($params=null) {

		if(is_null($params)) {
			$params = array();
		}

		if(array_key_exists('guzzle_client',$params)) {
			$this->guzzle = $params['guzzle_client'];
		} else {
			$this->guzzle = new \GuzzleHttp\Client;
		}

		if(array_key_exists('rpc_address',$params)) {
			$this->setRPCAddress($params['rpc_address']);
		} else {
			$this->setRPCAddress("http://localhost/RPC2");
		}
	}

	public function setRPCAddress($uri) {
		$this->rpc_address = $uri;
	}

	public function __call($method,$params) {
		$method = str_replace("_",".",$method);
		return $this->request($method,$params);
	}

	private function request($method,$params) {
		$xmlrpc_request = xmlrpc_encode_request($method,$params);
		$response = $this->guzzle->post($this->rpc_address,array('body'=>$xmlrpc_request));
		return xmlrpc_decode($response->getBody());
	}
}
