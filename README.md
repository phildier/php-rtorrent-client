#### PHP XMLRPC client for rtorrent

Provides a simple, composer-installable XMLRPC client for communicating with the rTorrent bittorrent client

#### Requirements

- PHP 5.5.0
- PHP curl extension
- PHP xmlrpc extension

#### Installation

Installation through composer is recommended:

`composer require phildier/php-rtorrent-client`

#### Example usage

```
<?php

require_once("../vendor/autoload.php");

$client = new PHPRtorrentClient\Client(array('rpc_address'=>"http://localhost:8981/RPC2"));

$request = new PHPRtorrentClient\Request("system.listMethods");

$methods = $client->exec($request);

print_r($methods->getAll());
```

#### Resources

rTorrent XMLRPC references:

- https://code.google.com/p/pyroscope/wiki/RtXmlRpcReference
- https://code.google.com/p/gi-torrent/wiki/rTorrent_XMLRPC_reference
- http://scratchpad.wikia.com/wiki/RTorrentCommands
