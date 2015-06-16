<?php

require_once("../vendor/autoload.php");

$c = new PHPRtorrentClient\Client(array('rpc_address'=>"http://localhost:8981/RPC2"));

// replace the dots in rtorrent's xmlrpc methods with underscores
$methods = $c->system_listMethods();

// OR, call this way:
$methods = $c->{"system.listMethods"}();

print_r($methods);
