<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandler;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorService;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\EventService;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandler;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\FieldsBasedWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherCounters;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherService;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettingsRepository;
use TSwiackiewicz\EventsCollector\Settings\SettingsRepository;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class EventControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event
 */
class EventControllerTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $event = 'event_type';

    /**
     * @var string
     */
    private $payload = '{"type":"test_event"}';

    /**
     * @test
     */
    public function shouldReturnAllRegisteredEventsAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->registerEvent($request);

        $response = $controller->getAllEvents();

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @return Request
     */
    private function createRequest()
    {
        return new Request(
            [
                'event' => $this->event
            ],
            [
                'event' => $this->event
            ],
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @return EventController
     */
    private function createEventController()
    {
        $events[$this->event] = Event::create($this->event);
        $settings = new InMemorySettingsRepository($events);

        $service = new EventService(
            $settings,
            new CollectorService(
                $settings,
                new CollectorAppenderHandlerFactory()
            ),
            new WatcherService(
                $settings,
                new WatcherActionHandlerFactory(),
                WatcherCounters::init()
            )
        );

        return new EventController($service);
    }

    /**
     * @param JsonResponse $response
     * @param $statusCode
     */
    private function assertResponse(JsonResponse $response, $statusCode)
    {
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldReturnEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $response = $controller->getEvent($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnNotRegisteredEvent()
    {
        $controller = $this->createEventControllerWithoutEventRegistered();
        $response = $controller->getEvent($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @return EventController
     */
    private function createEventControllerWithoutEventRegistered()
    {
        $settings = new InMemorySettingsRepository();

        $service = new EventService(
            $settings,
            new CollectorService(
                $settings,
                new CollectorAppenderHandlerFactory()
            ),
            new WatcherService(
                $settings,
                new WatcherActionHandlerFactory(),
                WatcherCounters::init()
            )
        );

        return new EventController($service);
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestResponseWhenReturningEventWithInvalidEventType()
    {
        $controller = $this->createEventController();
        $response = $controller->getEvent($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return Request
     */
    private function createRequestWithInvalidEventType()
    {
        $invalidEventType = [];

        return new Request(
            [
                'event' => $invalidEventType
            ],
            [
                'event' => $invalidEventType
            ],
            [],
            [],
            [],
            [],
            '{"type": []}'
        );
    }

    /**
     * @test
     */
    public function shouldUnregisterEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $response = $controller->unregisterEvent($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterNotRegisteredEvent()
    {
        $controller = $this->createEventControllerWithoutEventRegistered();
        $response = $controller->unregisterEvent($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestResponseWhenUnregisterEventWithInvalidEventType()
    {
        $controller = $this->createEventController();
        $response = $controller->unregisterEvent($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldRegisterEvent()
    {
        $controller = $this->createEventController();
        $response = $controller->registerEvent(
            $this->createRequest()
        );

        $this->assertResponse($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function shouldReturnConflictResponseWhenRegisterAlreadyRegisteredEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->registerEvent($request);

        $response = $controller->registerEvent($request);

        $this->assertResponse($response, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestResponseWhenRegisterEventWithInvalidEventType()
    {
        $controller = $this->createEventController();
        $response = $controller->registerEvent($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldCollectEvent()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->collectEvent($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @return EventController
     */
    private function createEventControllerWithRegisteredCollector(array $watcherCounters = [])
    {
        $settings = new InMemorySettingsRepository();
        $settings->registerEvent(Event::create($this->event));
        $settings->registerEventCollector($this->createCollector());
        $settings->registerEventWatcher($this->createWatcher());

        $service = new EventService(
            $settings,
            $this->createCollectorService($settings),
            $this->createWatcherService($settings, $watcherCounters)
        );

        return new EventController($service);
    }

    /**
     * @return Collector
     */
    private function createCollector()
    {
        $factory = new CollectorFactory();

        return $factory->createFromArray(
            $this->event,
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'name' => 'test_collector',
                'appender' => [
                    'type' => 'syslog',
                    'ident' => 'test'
                ]
            ]
        );
    }

    /**
     * @return Watcher
     */
    private function createWatcher()
    {
        $factory = new WatcherFactory();

        return $factory->createFromArray(
            $this->event,
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'name' => 'test_watcher',
                'threshold' => 100,
                'aggregator' => [
                    'type' => 'fields',
                    'fields' => [
                        'field1',
                        'field2'
                    ]
                ],
                'action' => [
                    'type' => 'email',
                    'to' => 'user@domain.com',
                    'subject' => 'Test subject'
                ]
            ]
        );
    }

    /**
     * @param SettingsRepository $settings
     * @return CollectorService
     */
    private function createCollectorService(SettingsRepository $settings)
    {
        $handler = new CollectorAppenderHandler(new NullLogger());

        $factory = $this->getMockBuilder(CollectorAppenderHandlerFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'createFromCollectorAppender'
                ]
            )
            ->getMock();
        $factory->expects($this->any())->method('createFromCollectorAppender')->willReturn($handler);

        return new CollectorService($settings, $factory);
    }

    /**
     * @param SettingsRepository $settings
     * @return WatcherService
     */
    private function createWatcherService(SettingsRepository $settings, array $watcherCounters)
    {
        $handler = $this->getMockBuilder(WatcherActionHandler::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $factory = $this->getMockBuilder(WatcherActionHandlerFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'createFromWatcherAction'
                ]
            )
            ->getMock();
        $factory->expects($this->any())->method('createFromWatcherAction')->willReturn($handler);

        return new WatcherService(
            $settings,
            $factory,
            new WatcherCounters($watcherCounters)
        );
    }

    /**
     * @test
     */
    public function shouldCollectEventWithActionHandled()
    {
        $counterKey = implode(
            FieldsBasedWatchedEventAggregator::KEY_PARTS_SEPARATOR,
            [
                $this->event,
                'field1',
                'field2'
            ]
        );
        $counters = [
            $counterKey => 100
        ];

        $controller = $this->createEventControllerWithRegisteredCollector($counters);
        $response = $controller->collectEvent($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenCollectingNotRegisteredEvent()
    {
        $controller = $this->createEventControllerWithoutEventRegistered();
        $response = $controller->collectEvent($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenNoCollectorsRegisteredForEvent()
    {
        $controller = $this->createEventController();
        $response = $controller->collectEvent($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenCollectingEventWithInvalidType()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->collectEvent($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenCollectingEventWithEmptyPayload()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->collectEvent($this->createRequestWithEmptyPayload());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return Request
     */
    private function createRequestWithEmptyPayload()
    {
        return new Request(
            [
                'event' => $this->event
            ],
            [
                'event' => $this->event
            ],
            [],
            [],
            [],
            [],
            ''
        );
    }
}
