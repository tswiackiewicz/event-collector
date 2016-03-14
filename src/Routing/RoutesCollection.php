<?php
namespace TSwiackiewicz\EventsCollector\Routing;

use FastRoute\DataGenerator\GroupCountBased as FastRouteDataGenerator;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std as FastRouteStdRouteParser;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherController;

/**
 * Class RoutesCollection
 * @package TSwiackiewicz\EventsCollector\Routing
 */
class RoutesCollection
{
    /**
     * @var FastRouteCollector
     */
    private $routes;

    /**
     * @param FastRouteCollector $routes
     */
    public function __construct(FastRouteCollector $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return RoutesCollection
     */
    public static function create()
    {
        return new RoutesCollection(
            new FastRouteCollector(
                new FastRouteStdRouteParser(),
                new FastRouteDataGenerator()
            )
        );
    }

    public function registerDefaultRoutes()
    {
        $this->routes->addRoute(
            'GET',
            '/event/',
            [EventController::class, 'getEvents']
        );
        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}',
            [EventController::class, 'getEvent']
        );
        $this->routes->addRoute(
            'POST',
            '/event/',
            [EventController::class, 'registerEvent']
        );
        $this->routes->addRoute(
            'DELETE',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}',
            [EventController::class, 'unregisterEvent']
        );

        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/collector/',
            [CollectorController::class, 'getEventCollectors']
        );
        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/collector/{collector:' . Collector::COLLECTOR_NAME_PATTERN . '}',
            [CollectorController::class, 'getEventCollector']
        );
        $this->routes->addRoute(
            'POST',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/collector/',
            [CollectorController::class, 'registerEventCollector']
        );
        $this->routes->addRoute(
            'DELETE',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/collector/{collector:' . Collector::COLLECTOR_NAME_PATTERN . '}',
            [CollectorController::class, 'unregisterEventCollector']
        );

        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/watcher/',
            [WatcherController::class, 'getEventWatchers']
        );
        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/watcher/{watcher:' . Watcher::WATCHER_NAME_PATTERN . '}',
            [WatcherController::class, 'getEventWatcher']
        );
        $this->routes->addRoute(
            'POST',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/watcher/',
            [WatcherController::class, 'registerEventWatcher']
        );
        $this->routes->addRoute(
            'DELETE',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/watcher/{watcher:' . Watcher::WATCHER_NAME_PATTERN . '}',
            [WatcherController::class, 'unregisterEventWatcher']
        );

        $this->routes->addRoute(
            'POST',
            '/event/{event:' . Event::EVENT_TYPE_PATTERN . '}/',
            [EventController::class, 'collectEvent']
        );
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes->getData();
    }
}