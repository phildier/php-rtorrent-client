<?php

require_once('../vendor/autoload.php');

$client = new \PHPRtorrentClient\Client([
	'rpc_address'	=>	'http://localhost:8981/RPC2',
]);

$torrent_file = sprintf('%s/%s',__DIR__,'ubuntu-15.04-desktop-amd64.iso.torrent');

var_dump($client->load($torrent_file));