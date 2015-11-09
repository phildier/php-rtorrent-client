<?php

namespace PHPRtorrentClient;

class Response {

	private $request = null;
	private $error_code = null;
	private $error_string = null;
	private $error = [];
	
	private $response;
	private $response_array = [];
	private $response_body;
	private $response_data;
	
	/*

	public function __construct(Request $request, \GuzzleHttp\Psr7\Response $response) {
		$this->request = $request;
		$this->response_body = $this->fixBody($response->getBody());
		$this->response = xmlrpc_decode($this->response_body);
		
		//print htmlspecialchars($body);
		//var_dump($this->response);
		
		$this->response = (!is_array($this->response) ? [$this->response] : $this->response);
		
		// Fill Error Variables
		foreach ($this->response AS $i => $response)
		{
			if (is_array($response) && array_key_exists('faultCode', $response))
			{
				$this->is_error = true;
				$this->error_code[$i] = $response['faultCode'];
				$this->error_string[$i] = $response['faultString'];
			}
		}
		
		$methods = $this->request->getMethods();
		$method_count = count($methods);

		foreach ($methods as $i => $method) {
			// Variables
			$data = ($method_count == 1 ? $this->response : $this->response[$i]);
			$count = count(is_array($data[0]) ? $data[0] : $data);
			$params = array_slice($method->getParams(), -($count));
			$keys = array_map([$this, 'sanitizeParamName'], $params);
			
			//var_dump('***', $method->getParams(), $params, $keys, $count);
			
			// Combine keys and values into Array
			if (is_array($data[0]) && count($keys) == count($data[0]))
			{
				foreach ($data AS $k => $row)
				{
					$data[$k] = array_combine($keys, array_map([$this, 'sanitizeParamValue'], $row));
				}
			}
			elseif (count($keys) == count($data))
			{
				$data = array_combine($keys, array_map([$this, 'sanitizeParamValue'], $data));
			}
			
			// Push data into Array
			$this->response_array[$method->getMethod()] = $data;
		}
	}
	*/
	
	public function isMultiRequest()
	{
		return $this->getRequest()->isMultiRequest();
	}
	
	public function __construct(Request $request, \GuzzleHttp\Psr7\Response $response)
	{
		// NOTE:	The XMLRPC Decoded Response Body can be:
		//
		//		1) a single value (int, string, etc)	- a single command
		//		2) an array				- a multicall
		//		3) a multidimentional array		- a system multicall containing many multicalls
		
		// Variables
		$this->request = $request;
		$this->response = $response;
		
		// Get Response Body -> Convert i8 to string (PHP XMLRPC doesnt not support 64bit ints) -> XMLRPC Decode Response Body -> Convert to normal Array if not an array already
		$this->response_body = $this->fixBody($this->getResponse()->getBody());
		$this->response_data = xmlrpc_decode($this->response_body);
		$this->response_data = (!is_array($this->response_data) ? [$this->response_data] : $this->response_data);
		
		// DEBUG
		/*
		print_r($this->getRequest()->getMethods());
		print_r(xmlrpc_decode($this->response_body));
		print_r($this->response_data);
		*/
		//exit;
		//print $this->getResponseBody(true);
		
		// Error Detection
		foreach ($this->response_data AS $i => $response)
		{
			if (is_array($response) && array_key_exists('faultCode', $response))
			{
				$this->error[] = ['code' => $response['faultCode'], 'string' => $response['faultString']];
			}
		}
		
		// Iterate Request Methods
		foreach ($this->getRequest()->getMethods() AS $index => $method)
		{
			// Variables
			$data = ($this->isMultiRequest() ? $this->response_data[$index][0] : $this->response_data);
			$data_count = count((is_array($data[0]) ? $data[0] : $data));
			
			$params = array_slice($method->getParams(), -($data_count));
			$keys = array_map([$this, 'sanitizeParamName'], $params);
			
			$count_match = (count($keys) == $data_count);
			
			// DEBUG
			//printf("Method: %s, Multi-Request: %s, Data Count: %s, Key Count: %s, Count Match: %s", $method->getMethod(), ($this->getRequest()->isMultiRequest() ? 'Yes' : 'No'), $data_count, count($keys), ($count_match ? 'Yes' : 'No'));
			//var_dump($method->getMethod(), $method->getParams());
			//var_dump($this->response_data[$index]);//exit;
			
			
			// Combine keys and values into Array
			if ($count_match && is_array($data[0]))
			{
				foreach ($data AS $k => $v)
				{
					//var_dump($keys, $k, $v, array_combine($keys, array_map([$this, 'sanitizeParamValue'], $v)));
					$data[$k] = array_combine($keys, array_map([$this, 'sanitizeParamValue'], $v));
				}
			}
			elseif ($count_match)
			{
				$data = array_combine($keys, array_map([$this, 'sanitizeParamValue'], (is_array($data) ? $data : [$data])));
			}
			
			// Push data into Array
			if ($this->isMultiRequest())
			{
				$this->response_array[$method->getMethod()][] = $data;
			}
			else
			{
				$this->response_array[$method->getMethod()] = $data;
			}
			
			
			
			
			
			
			/*
			
			return;
			
			var_dump($data);
			exit;
			
			
			// Swap indexes with keys if key number and data number match
			if ($count_match)
			{
				foreach ($data AS $k => $v)
				{
					$data[$keys[$k]] = $v;
					unset($data[$k]);
				}
				
				// Push into Array
				$this->response_array[$method->getMethod()] = array_merge((array)$this->response_array[$method->getMethod()], $data);
			}
			else
			{
				// Push into Array
				$this->response_array[$method->getMethod()] = array_merge((array)$this->response_array[$method->getMethod()], $data);
			}
			*/
		}
		
		/*
		// Collapse single element Arrays
		foreach ($this->response_array AS $method => $response)
		{
			if (count($response) == 1)
			{
				$this->response_array[$method] = reset($response);
			}
		}
		
		$this->response_array = (count($this->response_array) == 1 ? reset($this->response_array) : $this->response_array);
		*/
	}

	public function __toString() {
		return print_r($this->getLast(),true);
	}

	public function getAll() {
		if($this->isError()) {
			return false;
		}
		
		return $this->response_array;
	}

	public function getMethod($method_name) {
		if($this->isError()) {
			return false;
		}

		return $this->response_array[$method_name];
	}

	public function getLast() {
		if($this->isError()) {
			return false;
		}

		return array_pop($this->response_array);
	}

	public function isError()
	{
		return (count($this->error) ? true : false);
	}

	public function getError()
	{
		return $this->error;
	}

	/*
	public function getErrorCode() {
		return $this->error_code;
	}

	public function getErrorString() {
		return $this->error_string;
	}
	*/

	private function fixBody($str) {
		// PHP's xmlrpc_decode doesn't support i8 integers, so convert them to string
		$str = preg_replace('#<(/)?i8>#','<\\1string>',$str);
		return $str;
	}
	
	private function sanitizeParamName($value)
	{
		return preg_replace(['/=$/', '/.*=\$(.+)/', '/(\w+)=(\w+)$/', '/(\w+)\.(\w+)$/'], ['', '\\1', '\\2', '\\2'], $value);
	}
	
	private function sanitizeParamValue($value)
	{
		return urldecode(trim($value));
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getResponseBody($htmlspecialchars = false)
	{
		return ($htmlspecialchars ? htmlspecialchars($this->response_body) : $this->response_body);
	}

	public function getResponseData()
	{
		return $this->response_data;
	}

	public function getResponseArray($method = '')
	{
		return ($method ? $this->response_array[$method] : $this->response_array);
	}
}
