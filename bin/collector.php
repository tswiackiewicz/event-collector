<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TSwiackiewicz\EventsCollector\ControllerFactory;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Server;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

$routes = RoutesCollection::create();
$routes->registerDefaultRoutes();

$factory = new ControllerFactory(
    InMemorySettings::loadFromFile(),
    new InMemoryCounters()
);

$server = Server::create($routes, $factory);
$server->listen(getenv('PORT'), getenv('HOST'));
