<?php

require_once("../vendor/autoload.php");

$client = new PHPRtorrentClient\Client(array('rpc_address'=>"http://localhost:8981/RPC2"));

$request = new PHPRtorrentClient\Request("system.listMethods");

$methods = $client->exec($request);

print_r($methods->getAll());
