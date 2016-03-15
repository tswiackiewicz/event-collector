<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandler;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorService;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\EventService;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandler;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherController;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherService;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Settings\Settings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ControllerBaseTestCase
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event
 */
abstract class ControllerBaseTestCase extends BaseTestCase
{

    /**
     * @var string
     */
    protected $event = 'event_type';

    /**
     * @var string
     */
    protected $collector = 'test_collector';

    /**
     * @var string
     */
    protected $watcher = 'test_watcher';

    /**
     * @var string
     */
    protected $payload;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->payload = $this->buildPayload();
    }

    /**
     * @return string
     */
    abstract protected function buildPayload();

    /**
     * @param array $params
     * @return Request
     */
    protected function createRequest(array $params = [])
    {
        $requestParameters = array_merge(
            [
                'event' => $this->event
            ],
            $params
        );

        return new Request(
            $requestParameters,
            $requestParameters,
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @param array $params
     * @param string $payload
     * @return Request
     */
    protected function createRequestWithInvalidEventType(array $params = [], $payload = '')
    {
        $requestParameters = array_merge(
            [
                'event' => []
            ],
            $params
        );
        $requestPayload = $payload ?: $this->payload;

        return new Request(
            $requestParameters,
            $requestParameters,
            [],
            [],
            [],
            [],
            $requestPayload
        );
    }

    /**
     * @param array $params
     * @return Request
     */
    protected function createRequestWithEmptyPayload(array $params = [])
    {
        $requestParameters = array_merge(
            [
                'event' => $this->event
            ],
            $params
        );

        return new Request(
            $requestParameters,
            $requestParameters,
            [],
            [],
            [],
            [],
            ''
        );
    }

    /**
     * @return EventController
     */
    protected function createEventController()
    {
        $events[$this->event] = Event::create($this->event);
        $settings = new InMemorySettings($events);

        $service = new EventService(
            $settings,
            new CollectorService(
                $settings,
                new CollectorAppenderHandlerFactory()
            ),
            new WatcherService(
                $settings,
                new WatcherActionHandlerFactory(),
                new InMemoryCounters()
            )
        );

        return new EventController($service);
    }

    /**
     * @return EventController
     */
    protected function createEventControllerWithoutEventRegistered()
    {
        $settings = new InMemorySettings();

        $service = new EventService(
            $settings,
            new CollectorService(
                $settings,
                new CollectorAppenderHandlerFactory()
            ),
            new WatcherService(
                $settings,
                new WatcherActionHandlerFactory(),
                new InMemoryCounters()
            )
        );

        return new EventController($service);
    }

    /**
     * @return CollectorController
     */
    protected function createCollectorController()
    {
        $events[$this->event] = Event::create($this->event);
        $service = new CollectorService(
            new InMemorySettings($events),
            new CollectorAppenderHandlerFactory()
        );

        return new CollectorController($service, new CollectorFactory());
    }

    /**
     * @return CollectorController
     */
    protected function createCollectorControllerWithoutEventRegistered()
    {
        $service = new CollectorService(
            new InMemorySettings(),
            new CollectorAppenderHandlerFactory()
        );

        return new CollectorController($service, new CollectorFactory());
    }

    /**
     * @return WatcherController
     */
    protected function createWatcherController()
    {
        $events[$this->event] = Event::create($this->event);

        $service = new WatcherService(
            new InMemorySettings($events),
            new WatcherActionHandlerFactory(),
            new InMemoryCounters()
        );

        return new WatcherController(
            $service,
            new WatcherFactory()
        );
    }

    /**
     * @return WatcherController
     */
    protected function createWatcherControllerWithoutEventRegistered()
    {
        $service = new WatcherService(
            new InMemorySettings(),
            new WatcherActionHandlerFactory(),
            new InMemoryCounters()
        );

        return new WatcherController(
            $service,
            new WatcherFactory()
        );
    }

    /**
     * @param array $watcherCounters
     * @return EventController
     */
    protected function createEventControllerWithRegisteredCollector(array $watcherCounters = [])
    {
        $settings = new InMemorySettings();
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
                'name' => $this->collector,
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
                'name' => $this->watcher,
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
     * @param Settings $settings
     * @return CollectorService
     */
    private function createCollectorService(Settings $settings)
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
     * @param Settings $settings
     * @param array $watcherCounters
     * @return WatcherService
     */
    private function createWatcherService(Settings $settings, array $watcherCounters)
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
            new InMemoryCounters($watcherCounters)
        );
    }

    /**
     * @param JsonResponse $response
     * @param $statusCode
     */
    protected function assertResponse(JsonResponse $response, $statusCode)
    {
        $this->assertEquals($statusCode, $response->getStatusCode());
    }
}