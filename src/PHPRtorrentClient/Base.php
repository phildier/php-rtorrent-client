<?php

namespace PHPRtorrentClient;

class Base {

	protected $client;
	protected $data = [];
	protected $commands = [];
	protected $aliases = [];
	protected $variable_cache = [];

	function __construct(&$client, $data)
	{
		$this->client = $client;
		$this->data = $data;
	}
	
	public function __toString()
	{
		return sprintf('%s', $this->getName());
	}
	
	public function __call($name, $arguments)
	{
		// Commands
		if (array_key_exists($name, $this->commands))
		{
			return $this->execute($this->commands[$name]);
		}
		
		// Find variable based on name
		if ($this->__findVariable($name) !== NULL)
		{
			return $this->data[$this->__findVariable($name)];
		}

		// Failure
		$trace = debug_backtrace();
		trigger_error(sprintf('Undefined property via __call(): %s in %s on line %s', $name, $trace[0]['file'], $trace[0]['line']), E_USER_NOTICE);
		return null;
	}
	
	private function __findVariable($name)
	{
		if ($this->variable_cache[$name])
		{
			return $this->variable_cache[$name];
		}
		
		$this->variable_cache[$name] = null;
		
		try
		{
			$try = 'get_' . $name;
			
			if (isset($this->data[$name]))
			{
				throw new VariableFoundException($name);
			}
			
			if (isset($this->data[$try]))
			{
				throw new VariableFoundException($try);
			}
		
			if (array_key_exists($name, $this->aliases))
			{
				throw new VariableFoundException($this->aliases[$name]);
			}
			
			if (array_key_exists($try, $this->aliases))
			{
				throw new VariableFoundException($this->aliases[$try]);
			}
			
			if (preg_match('/[A-Z]/', $name))
			{
				$name = strtolower(preg_replace('/(?<!^)([A-Z])/', '_\\1', $name));
				$try = 'get_' . $try;
				
				if (isset($this->data[$name]))
				{
					throw new VariableFoundException($name);
				}
				
				if (isset($this->data[$try]))
				{
					throw new VariableFoundException($try);
				}
		
				if (array_key_exists($name, $this->aliases))
				{
					throw new VariableFoundException($this->aliases[$name]);
				}
				
				if (array_key_exists($try, $this->aliases))
				{
					throw new VariableFoundException($this->aliases[$try]);
				}
			}
		}
		catch (VariableFoundException $e)
		{
			//printf('Found Variable Name: %s = %s<br>', $name, $e->getMessage());
			
			$this->variable_cache[$name] = $e->getMessage();
		}
		
		return $this->variable_cache[$name];
	}
	
	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	public function __get($name)
	{
		return $this->get($name);
	}
	
	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}

	public function get($name = '')
	{
		if (!$name)
		{
			return (array)$this->data;
		}
	
		if ($this->__findVariable($name) !== NULL)
		{
			return $this->data[$this->__findVariable($name)];
		}

		$trace = debug_backtrace();
		trigger_error(sprintf('Undefined property via __get(): %s in %s on line %s', $name, $trace[0]['file'], $trace[0]['line']), E_USER_NOTICE);
		return null;
	}

	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	public function __unset($name)
	{
		unset($this->data[$name]);
	}

	// Wrappers for Client
	public function exec(Request $request)
	{
		return $this->client->exec($request);
	}
	
	public function execute($method, $params = [])
	{
		if (is_array($method) && is_array(reset($method)))
		{
			foreach ($method AS $m => $p)
			{
				$method[$m] = array_merge([$this->getHash()], $p);
			}
		}
		else
		{
			$params = array_merge([$this->getHash()], (array)$params);
		}
	
		return $this->client->execute($method, $params);
	}
	
	public function request($method, $params, $class, &$cache = '')
	{
		return $this->client->request($method, array_merge([$this->getHash()], $params), $class, $cache);
	}
}
