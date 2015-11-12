<?php

namespace PHPRtorrentClient;

class VariableFoundException extends \Exception {}

class Torrent extends \PHPRtorrentClient\Base
{
	protected $commands = [
		'start'		=> [
			'd.open'	=> [],
			'd.start'	=> [],
		],
		'stop'		=> [
			'd.stop'	=> [],
			'd.close'	=> [],
		],
		'open'		=> 'd.open',
		'close'		=> 'd.close',
		'erase'		=> 'd.erase',
		'delete'	=> [
			'd.set_custom5' => ['1'],
			'd.delete_tied'	=> [],
			'd.erase'	=> [],
		],
	];
/*
d.add_peer
d.check_hash               Initiate a hash check
d.close                    Stop the torrent
d.create_link
d.delete_link
d.delete_tied
d.erase                    Erase the torrent from the list
d.get_base_filename
d.get_base_path
d.get_bitfield
d.get_bytes_done
d.get_chunk_size           Get the size of a block of data (chunk)
d.get_chunks_hashed
d.get_complete
d.get_completed_bytes
d.get_completed_chunks
d.get_connection_current
d.get_connection_leech
d.get_connection_seed
d.get_creation_date        Get the date the torrent was created
d.get_custom1
d.get_custom2
d.get_custom3
d.get_custom4
d.get_custom5
d.get_custom_throw
d.get_directory
d.get_directory_base
d.get_down_rate             Get the speed in bytes/sec in which the torrent is downloading
d.get_down_total
d.get_free_diskspace
d.get_hash                  Always query the hash, since it is the index for other calls.
d.get_hashing
d.get_hashing_failed
d.get_ignore_commands
d.get_left_bytes
d.get_loaded_file
d.get_local_id
d.get_local_id_html
d.get_max_file_size
d.get_max_size_pex
d.get_message
d.get_mode
d.get_name                  The name of the torrent.
d.get_peer_exchange
d.get_peers_accounted       The number of leechers
d.get_peers_complete        The number of complete peers = seeders
d.get_peers_connected
d.get_peers_max
d.get_peers_min
d.get_peers_not_connected   Get the peers rtorrent sees but is not connected to
d.get_priority              Get the priority (0=off, 1=low, 2=normal, 3=high)
d.get_priority_str          Get the priority as a string (Off, Low, Normal, High)
d.get_ratio                 Get the ratio (upload divided by download)
d.get_size_bytes            Get the torrent size in bytes
d.get_size_chunks           Get the size of the torrent in chunks
d.get_size_files            
d.get_size_pex
d.get_skip_rate
d.get_skip_total
d.get_state
d.get_state_changed
d.get_state_counter
d.get_throttle_name
d.get_tied_to_file
d.get_tracker_focus
d.get_tracker_numwant
d.get_tracker_size
d.get_up_rate              Get the speed in bytes/sec in which the torrent is uploading
d.get_up_total
d.get_uploads_max
d.initialize_logs
d.is_active                Get the active state (0=inactive, 1=active)
d.is_hash_checked
d.is_hash_checking         Get the hash state (0=not hash checking, 1=hash checking)
d.is_multi_file
d.is_open                  Get the state of the torrent (0=closed, 1=open)
d.is_pex_active
d.is_private               Get the privacy of the torrent (0=public, 1=private)
d.open                     
d.pause
d.resume
d.save_session
d.set_connection_current
d.set_custom1
d.set_custom2
d.set_custom3
d.set_custom4
d.set_custom5
d.set_directory
d.set_directory_base
d.set_hashing_failed
d.set_ignore_commands
d.set_max_file_size
d.set_message
d.set_peer_exchange
d.set_peers_max
d.set_peers_min
d.set_priority              Set the priority (0 = off (do not allocate up/down slots), 1=low, 2=normal, 3=high)
d.set_throttle_name
d.set_tied_to_file
d.set_tracker_numwant
d.set_uploads_max
d.start                     Start the torrent
d.stop                      Stop the torrent
d.try_close
d.try_start
d.try_stop
d.update_priorities         Update the torrent after changes to file priorities
d.views
d.views.has
d.views.push_back
d.views.push_back_unique
d.views.remove
*/
	
	protected $aliases = [
		'get_label'	=> 'get_custom1',
	];
	
	public $trackers;
	public $files;
	
	public static $default_tracker_method = 't.multicall';
	public static $default_tracker_params = [
		't.get_url=',
		't.get_type=',
		't.is_enabled=',
		't.get_group=',
		't.get_scrape_complete=',
		't.get_scrape_incomplete=',
		't.get_scrape_downloaded=',
		't.get_normal_interval=',
		't.get_scrape_time_last=',
	];
	
	public static $default_file_method = 'f.multicall';
	public static $default_file_params = [
		'f.get_path=',
		'f.get_completed_chunks=',
		'f.get_size_chunks=',
		'f.get_size_bytes=',
		'f.get_priority=',
		'f.prioritize_first=',
		'f.prioritize_last=',
	];

	public function getFiles($params = [])
	{
		// Create Params -> Request -> Create Object Array using specified Class -> Save to Variable -> Return
		return $this->request(self::$default_file_method, array_merge([''], (count($params) ? $params : self::$default_file_params)), '\PHPRtorrentClient\File', $this->files);
	}

	public function getFilesArray($params = [])
	{
		foreach ($this->getFiles() AS $file)
		{
			$results[] = $file->getPath();
		}
		
		return (array)$results;
	}
	
	// NOTE: Use \PHPRtorrentClient\Client->getTorrentsByFile if you are doing more than 1 Torrent
	public function matchFile($find)
	{
		// Iterate Domains
		foreach ($this->getFilesArray() AS $file)
		{
			// Match
			if (preg_match('/' . $find . '/i', $file))
			{
				// Success !
				return true;
			}
		}
		
		// Failure
		return false;
	}

	public function getTrackers($params = [])
	{
		// Create Params -> Request -> Create Object Array using specified Class -> Save to Variable -> Return
		return $this->request(self::$default_tracker_method, array_merge([''], (count($params) ? $params : self::$default_tracker_params)), '\PHPRtorrentClient\Tracker', $this->trackers);
	}

	public function getTrackerDomains()
	{
		// Iterate Trackers
		foreach ($this->getTrackers() AS $tracker)
		{
			// Push Domain into Array
			$domains[] = $tracker->getDomain();
		}
	
		// Return
		return $domains;
	}
	
	// NOTE: Use \PHPRtorrentClient\Client->getTorrentsByTracker if you are doing more than 1 Torrent
	public function matchTrackerDomain($find)
	{
		// Iterate Domains
		foreach ($this->getTrackerDomains() AS $domain)
		{
			// Match
			if (preg_match('/' . $find . '/i', $domain))
			{
				// Success !
				return true;
			}
		}
		
		// Failure
		return false;
	}
	
	public function setLabel($label)
	{
		return $this->execute('d.set_custom1', $label);
	}
	
	public function isRunning()
	{
		return ($this->getState() == 1 ? true : false);
	}
	
	public function isStopped()
	{
		return !$this->isRunning();
	}
	
	public function isFinished()
	{
		return ($this->getLeftBytes() ? false : true);
	}
	
	public function isComplete()
	{
		return $this->isFinished();
	}
	
	public function isDownloading()
	{
		return ($this->isFinished() ? false : true);
	}
	
	public function getRatio()
	{
		return (!empty($this->get('get_ratio')) ? ($this->get('get_ratio') / 1000) : 0);
	}
}
