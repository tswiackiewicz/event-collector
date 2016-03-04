<?php
namespace TSwiackiewicz\EventsCollector\Tests\Integration;

use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Server;
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

        $server = Server::create($routes, new Configuration());

        $this->assertInstanceOf(Server::class, $server);
    }
}
