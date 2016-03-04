<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Server;

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

$routes = RoutesCollection::create();
$routes->registerDefaultRoutes();

$configuration = Configuration::loadFromFile();

$server = Server::create($routes, $configuration);
$server->listen(getenv('PORT'), getenv('HOST'));
