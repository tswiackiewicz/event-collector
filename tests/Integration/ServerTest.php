<?php
namespace TSwiackiewicz\EventsCollector\Tests\Integration;

use TSwiackiewicz\EventsCollector\ControllerFactory;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Server;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ServerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Integration
 */
class ServerTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateServer()
    {
        $routes = RoutesCollection::create();
        $routes->registerDefaultRoutes();

        $factory = new ControllerFactory(
            new InMemorySettings(),
            new InMemoryCounters()
        );

        $server = Server::create($routes, $factory);

        $this->assertInstanceOf(Server::class, $server);
    }
}
