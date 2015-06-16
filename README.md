#### PHP XMLRPC client for rtorrent

Provides a composer-installable XMLRPC client for communicating with the rTorrent bittorrent client

#### Requirements

PHP 5.5.0
PHP curl extension
PHP xmlrpc extension

#### Installation

Installing through composer is recommended.

`composer install phildier/php-rtorrent-client`

#### Example usage

```
$c = new PHPRtorrentClient\Client;

$c->setRPCAddress("http://localhost:8180/RPC2");

$methods = $c->system_listMethods();
```
