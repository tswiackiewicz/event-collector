<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Server;

$configuration = new Configuration();

$routes = RoutesCollection::create();
$routes->registerDefaultRoutes();

$server = Server::create($routes, $configuration);
$server->listen(1234, '0.0.0.0');
