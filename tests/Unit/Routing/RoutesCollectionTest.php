<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Routing;

use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class RoutesCollectionTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Routing
 */
class RoutesCollectionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldReturnRegisteredRoutes()
    {
        $routes = RoutesCollection::create();
        $routes->registerDefaultRoutes();

        $this->assertNotEmpty($routes->getRoutes());
    }
}
