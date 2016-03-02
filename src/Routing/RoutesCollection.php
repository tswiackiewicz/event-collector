<?php
namespace TSwiackiewicz\EventsCollector\Routing;

use FastRoute\DataGenerator\GroupCountBased as FastRouteDataGenerator;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std as FastRouteStdRouteParser;
use TSwiackiewicz\EventsCollector\Action\Action;
use TSwiackiewicz\EventsCollector\Action\ActionController;
use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventController;

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
            [EventController::class, 'getAllEventTypes']
        );
        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}',
            [EventController::class, 'getEventType']
        );
        $this->routes->addRoute(
            'DELETE',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}',
            [EventController::class, 'unregisterEventType']
        );
        $this->routes->addRoute(
            'POST',
            '/event/',
            [EventController::class, 'registerEventType']
        );

        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/collector/',
            [CollectorController::class, 'getAllEventCollectors']
        );
        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/collector/{collector:' . Collector::VALID_COLLECTOR_NAME_PATTERN. '}',
            [CollectorController::class, 'getEventCollector']
        );
        $this->routes->addRoute(
            'DELETE',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/collector/{collector:' . Collector::VALID_COLLECTOR_NAME_PATTERN. '}',
            [CollectorController::class, 'unregisterEventCollector']
        );
        $this->routes->addRoute(
            'POST',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/collector/',
            [CollectorController::class, 'registerEventCollector']
        );

        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/action/',
            [ActionController::class, 'getAllEventActions']
        );
        $this->routes->addRoute(
            'GET',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/action/{action:' . Action::VALID_ACTION_NAME_PATTERN. '}',
            [ActionController::class, 'getEventAction']
        );
        $this->routes->addRoute(
            'DELETE',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/action/{action:' . Action::VALID_ACTION_NAME_PATTERN. '}',
            [ActionController::class, 'unregisterEventAction']
        );
        $this->routes->addRoute(
            'POST',
            '/event/{event:' . Event::VALID_EVENT_TYPE_PATTERN . '}/action/',
            [ActionController::class, 'registerEventAction']
        );

        //$this->routes->addRoute('POST', '/event/{event_type}/', [EventController::class, 'collectEvent']);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes->getData();
    }
}