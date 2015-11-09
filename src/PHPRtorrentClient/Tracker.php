<?php

namespace PHPRtorrentClient;

class Tracker extends \PHPRtorrentClient\Base
{
	public function getDomain()
	{
		// Find Domain in Tracker Announce Url
		preg_match('/^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/', $this->getUrl(), $matches);
		
		// Return
		return ($matches[6] ? $matches[6] : null);
	}
	
	public function match($find)
	{
		return ((preg_match('/' . $find . '/i', $this->getDomain()) ? true : false));
	}

/*
't.get_group'
't.get_id'
't.get_min_interval'
't.get_normal_interval'
't.get_scrape_complete'       Get the complete peers registered on the tracker
't.get_scrape_downloaded'
't.get_scrape_incomplete'     Get the incomplete peers registered on the tracker
't.get_scrape_time_last'
't.get_type'                  Get the tracker type (1=http, 2=udp, 3=dht)
't.get_url'                   Get the url for the tracker
't.is_enabled'                Get the status of the tracker (0=disabled, 1=enabled)
't.is_open'                   Get the status of the tracker (0=closed, 1=open)
't.set_enabled'               Enable the tracker
*/
}
