<?php

namespace PHPRtorrentClient;

class Client {

	private $guzzle = null;
	private $rpc_address = null;
	public $torrents = [];
	public $torrentsHashes = [];
	
	public $default_rpc_address = 'http://localhost/RPC2';
	
	public $default_torrent_method = 'd.multicall';
	public $default_torrent_params = [
			// Main view
			//'main',			//  'main', 'started','stopped','hashing','seeding'
			
			// Request these fields for each torrent
	/* 0  */	'd.get_hash=',
	/* 1  */	'd.is_open=',
	/* 2  */	'd.is_hash_checking=',
	/* 3  */	'd.is_hash_checked=',
	/* 4  */	'd.get_state=',		// 0 = stopped, 1 = running (upload or download)
			
	/* 5  */	'd.get_name=',
	/* 6  */	'd.get_size_bytes=',
	/* 7  */	'd.get_completed_chunks=',
	/* 8  */	'd.get_size_chunks=',
	/* 9  */	'd.get_bytes_done=',
			
	/* 10  */	'd.get_up_total=',
	/* 11  */	'd.get_ratio=',
	/* 12  */	'd.get_up_rate=',
	/* 13  */	'd.get_down_rate=',
	/* 14  */	'd.get_chunk_size=',
			
	/* 15  */	'd.get_custom1=',
	/* 16  */	'd.get_peers_accounted=',
	/* 17  */	'd.get_peers_not_connected=',
	/* 18  */	'd.get_peers_connected=',
	/* 19  */	'd.get_peers_complete=',
			
	/* 20  */	'd.get_left_bytes=',	// if === 0 then download is complete, no bytes remaining to download
	/* 21  */	'd.get_priority=',
	/* 22  */	'd.get_state_changed=',
	/* 23  */	'd.get_skip_total=',
	/* 24  */	'd.get_hashing=',
			
	/* 25  */	'd.get_chunks_hashed=',
	/* 26  */	'd.get_base_path=',
	/* 27  */	'd.get_creation_date=',
	/* 28  */	'd.get_tracker_focus=',
	/* 29  */	'd.is_active=',
			
	/* 30  */	'd.get_message=',
	/* 31  */	'd.get_custom2=',
	/* 32  */	'd.get_free_diskspace=',
	/* 33  */	'd.is_private=',
	/* 34  */	'd.is_multi_file=',
	
	/* 35  */	'cat=$d.views=',
	/* 36  */	'd.get_custom=seedingtime',
	/* 37  */	'd.get_custom=addtime',
	];
	
	public function __construct($params = array())
	{
		// Guzzle Client
		$this->guzzle = ((!empty($params['guzzle_client']) && ($params['guzzle_client'] InstanceOf \GuzzleHttp\Client)) ? $params['guzzle_client'] : new \GuzzleHttp\Client);
		
		// RPC Address
		$this->setRPCAddress((!empty($params['rpc_address']) ? $params['rpc_address'] : $this->default_rpc_address));
	}

	public function setRPCAddress($uri)
	{
		$this->rpc_address = $uri;
	}

	public function send(Request $request)
	{
		return new Response($request, $this->guzzle->post($this->rpc_address, array('body' => (string)$request)));
	}
	
	public function execute($method, $params = [])
	{
		// Build Request
		$request = new \PHPRtorrentClient\Request();
		
		// Multi-Request
		if (is_array($method))
		{
			foreach ($method AS $m => $p)
			{
				$request->addMethod($m, (array)$p);
			}
		}
		// Request
		else
		{
			$request->addMethod($method, (array)$params);
		}
		
		// Execute -> Return Response Object
		return $this->send($request);
	}
	
	public function request($method, $params, $class, &$cache = '')
	{
		if ($cache)
		{
			//printf('using cache for: %s<br>', $method);
			return $cache;
		}
		
		// Request -> Get Result -> Make Torrent Object Array
		foreach ($this->execute($method, $params)->getMethod($method) AS $i => $data)
		{
			// Multi-Request
			if (is_array($data[0]))
			{
				foreach ($data AS $result)
				{
					$results[] = new $class($this, $result);
				}
			}
			// Request
			else
			{
				$results[] = new $class($this, $data);
			}
		}
		
		// Save to referenced cache variable if set
		$cache = $results;
		
		// Return
		return $results;
	}
	
	public function getTorrents($view = 'main', $params = array())
	{
		// Return
		return $this->request($this->default_torrent_method, array_merge([$view], (count($params) ? $params : $this->default_torrent_params)), '\PHPRtorrentClient\Torrent', $this->torrents[$view]);
	}
	
	public function getTorrentsByTracker($find, $view = 'main')
	{
		// Start Request
		$request = new \PHPRtorrentClient\Request();
		
		// Get All Torrents
		$torrents = $this->getTorrents($view);
		
		// Iterate Torrents
		foreach ($torrents AS $torrent)
		{
			// Push Method into Request for Torrent
			$request->addMethod(\PHPRtorrentClient\Torrent::$default_tracker_method, array_merge([$torrent->getHash(), ''], \PHPRtorrentClient\Torrent::$default_tracker_params));
		}

		// Send Request -> Get Response Array
		foreach ($this->send($request)->getMethod(\PHPRtorrentClient\Torrent::$default_tracker_method) AS $index => $trackers)
		{
			// Iterate
			foreach ($trackers AS $tracker)
			{
				// Create Object
				$object = new \PHPRtorrentClient\Tracker($this, $tracker);
			
				// Match find arg vs scrape domain
				if ($object->match($find))
				{
					// Push into Array
					$results[] = $torrents[$index];
					
					// Move to next result set
					continue 2;
				}

			}
		}
		
		// Return
		return (array)$results;
	}
	
	public function getTorrentsByFile($find, $view = 'main')
	{
		// Start Request
		$request = new \PHPRtorrentClient\Request();
		
		// Get All Torrents
		$torrents = $this->getTorrents($view);
		
		// Iterate Torrents
		foreach ($torrents AS $torrent)
		{
			// Push Method into Request for Torrent
			$request->addMethod(\PHPRtorrentClient\Torrent::$default_file_method, array_merge([$torrent->getHash(), ''], \PHPRtorrentClient\Torrent::$default_file_params));
		}

		// Send Request -> Get Response Array
		foreach ($this->send($request)->getMethod(\PHPRtorrentClient\Torrent::$default_file_method) AS $index => $files)
		{
			// Iterate Files
			foreach ($files AS $file)
			{
				// Create Object
				$object = new \PHPRtorrentClient\File($this, $file);
			
				// Match find arg vs scrape domain
				if ($object->match($find))
				{
					// Push into Array
					$results[] = $torrents[$index];
					
					// Move to next File
					continue 2;
				}

			}
		}
		
		// Return
		return (array)$results;
	}
	/*
	public function deleteTorrents($torrents)
	{
		// Start Request
		$request = new \PHPRtorrentClient\Request();
		
		// Iterate Torrents
		foreach ($torrents AS $torrent)
		{
			$
			// Push Method into Request for Torrent
			$request->addMethod(\PHPRtorrentClient\Torrent::$default_file_method, array_merge([$torrent->getHash(), ''], \PHPRtorrentClient\Torrent::$default_file_params));
		}

		// Send Request -> Get Response Array
		foreach ($this->send($request)->getMethod(\PHPRtorrentClient\Torrent::$default_file_method) AS $index => $files)
		{
			// Iterate Files
			foreach ($files AS $file)
			{
				// Create Object
				$object = new \PHPRtorrentClient\File($this, $file);
			
				// Match find arg vs scrape domain
				if ($object->match($find))
				{
					// Push into Array
					$results[] = $torrents[$index];
					
					// Move to next File
					continue 2;
				}

			}
		}
		
		// Return
		return (array)$results;
	}
	*/
	public function listMethods()
	{
		return $this->execute('system.listMethods')->getLast();
	}
	
	public function methodSignature($method)
	{
		return $this->execute('system.methodSignature', [$method])->getLast();
	}
	
	public function methodHelp($method)
	{
		return $this->execute('system.methodHelp', [$method])->getLast();
	}
	
	public function methodHelpAll()
	{
		$request = new \PHPRtorrentClient\Request();

		foreach ($this->listMethods() AS $method)
		{
			$request->addMethod('system.methodHelp', [$method]);
		}

		return $this->send($request)->getResponseArray();
	}
	
	public function shutdown()
	{
		return $this->execute('system.shutdown')->getLast();
	}
	
	public function load($url, $load_started = true)
	{
		return $this->execute('load' . ($load_started ? '_start' : ''), [$url])->getLast();
	}

	// Deprecated function
	public function exec(Request $request)
	{
		return $this->send($request);
	}
	
	public function OLDgetTorrentsByTracker($find, $view = 'main')
	{
		foreach ($this->getTorrents($view) AS $torrent)
		{
			if ($torrent->matchTrackerDomain($find))
			{
				$results[] = $torrent;
			}
		}
		
		return (array)$results;
	}
	
	public function OLDgetTorrentsByFile($find, $view = 'main')
	{
		foreach ($this->getTorrents($view) AS $torrent)
		{
			if ($torrent->matchFile($find))
			{
				$results[] = $torrent;
			}
		}
		
		return (array)$results;
	}
}
