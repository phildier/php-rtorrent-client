<?php

require_once('../vendor/autoload.php');

$client = new \PHPRtorrentClient\Client([
	'rpc_address'	=>	'http://localhost:8981/RPC2',
]);

$find = ($_REQUEST['find'] ? $_REQUEST['find'] : 'family.guy');
$view = ($_REQUEST['view'] ? $_REQUEST['view'] : 'main');

printf('Finding Torrents: Keyword: %s, View: %s<br>', $find, $view);

$torrents = $client->getTorrentsByFile($find, $view);

printf('Total Torrents: %s<br>', count($torrents));

foreach ($torrents AS $torrent)
{
	printf('Torrent: %s<br>', $torrent->getName());
	
	//printf('Torrent: %s, Files: %s<br>', $torrent->getName(), implode(', ', $torrent->getFilesArray()));
	//printf('Torrent: %s, Trackers: %s<br>', $torrent->getName(), implode(', ', $torrent->getTrackerDomains()));
}