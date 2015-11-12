<?php

require_once('../vendor/autoload.php');

$client = new \PHPRtorrentClient\Client([
	'rpc_address'	=>	'http://localhost:8981/RPC2',
]);

foreach ($client->getTorrents() AS $i => $torrent)
{
	printf("%s: %s (%s), Running: %s, Label: %s\n", ($i + 1), $torrent->getName(), $torrent->getHash(), ($torrent->isActive() ? 'Yes' : 'No'), $torrent->getLabel());
}