<?php

namespace PHPRtorrentClient;

class Client {

	private $guzzle = null;
	private $rpc_address = null;

	public function __construct($params=null) {

		if(is_null($params)) {
			$params = array();
		}

		if(array_key_exists('guzzle_client',$params) && !empty($params['guzzle_client'])) {
			$this->guzzle = $params['guzzle_client'];
		} else {
			$this->guzzle = new \GuzzleHttp\Client;
		}

		if(array_key_exists('rpc_address',$params) && !empty($params['rpc_address'])) {
			$this->setRPCAddress($params['rpc_address']);
		} else {
			$this->setRPCAddress("http://localhost/RPC2");
		}
	}

	public function setRPCAddress($uri) {
		$this->rpc_address = $uri;
	}

	public function exec(Request $request) {
		$response = $this->guzzle->post($this->rpc_address,array('body'=>(string)$request));
		return new Response($request,$response);
	}
}
