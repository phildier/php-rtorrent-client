<?php

namespace PHPRtorrentClient;

class File extends \PHPRtorrentClient\Base
{
	public function match($find)
	{
		// Match
		if (preg_match('/' . $find . '/i', $this->getPath()))
		{
			// Success !
			return true;
		}
		
		// Failure
		return false;
	}
/*
'f.get_completed_chunks'       Get the chunks that already downloaded
'f.get_frozen_path'
'f.get_last_touched'           Last time the file was touched in microseconds since 1970
'f.get_match_depth_next'
'f.get_match_depth_prev'
'f.get_offset'
'f.get_path'                   Get the path of the file
'f.get_path_components'         
'f.get_path_depth'
'f.get_priority'               Get the priority (0=do not download, 1=normal, 2=high)
'f.get_range_first'            Get the chunk range start
'f.get_range_second'           Get the chunk range end
'f.get_size_bytes'             Get the size of the file in bytes
'f.get_size_chunks'            Get the size of the file in chunks
'f.is_create_queued'
'f.is_created'
'f.is_open'                    Get the state of the file (0=closed, 1=open)
'f.is_resize_queued'
'f.set_create_queued'
'f.set_priority'               Set the priority for the file (0=do not download, 1=normal, 2=high)
'f.set_resize_queued'
'f.unset_create_queued'
'f.unset_resize_queued'
*/
}
