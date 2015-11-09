<?php

require_once('../vendor/autoload.php');

$client = new \PHPRtorrentClient\Client([
	'rpc_address'	=>	'http://localhost:8981/RPC2',
]);

var_dump($client->listMethods());