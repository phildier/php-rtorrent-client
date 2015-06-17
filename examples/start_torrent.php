<?php

require_once("../vendor/autoload.php");

$torrent_file = sprintf("%s/%s",__DIR__,"ubuntu-15.04-desktop-amd64.iso.torrent");
$save_dir = "/tmp/torrent";

// create new client and override default rpc address
$client = new PHPRtorrentClient\Client(array('rpc_address'=>"http://localhost:8981/RPC2"));

// begin new request
$request = new PHPRtorrentClient\Request();

// creates download directory using rtorrent
$request->addMethod("execute",array("mkdir","-p",$save_dir));

// method starts torrent and sets download directory
$request->addMethod("load_start",array($torrent_file,"d.set_directory_base=\"$save_dir\""));

// execute request
$response = $client->exec($request);

// get all method return values
print_r($response->getAll());

// get a particular return value
print_r($response->getMethod("execute"));

// get a particular return value
print_r($response->getLast());
