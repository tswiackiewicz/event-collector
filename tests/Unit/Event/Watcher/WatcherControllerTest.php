<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherController;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherCounters;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherService;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettingsRepository;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher
 */
class WatcherControllerTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $event = 'test_event';

    /**
     * @var string
     */
    private $watcher = 'test_watcher';

    /**
     * @var string
     */
    private $payload = '{"name":"test_watcher","threshold":100,"aggregator":{"type":"single"},"action":{"type":"email","to":"user@domain.com","subject":"Test subject"}}';

    /**
     * @test
     */
    public function shouldReturnEventWatchers()
    {
        $request = $this->createRequest();

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->getEventWatchers($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @return Request
     */
    private function createRequest()
    {
        return new Request(
            [
                'event' => $this->event,
                'watcher' => $this->watcher
            ],
            [
                'event' => $this->event,
                'watcher' => $this->watcher
            ],
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @return WatcherController
     */
    private function createWatcherController()
    {
        $events[$this->event] = Event::create($this->event);

        $service = new WatcherService(
            new InMemorySettingsRepository($events),
            new WatcherActionHandlerFactory(),
            WatcherCounters::init()
        );

        return new WatcherController(
            $service,
            new WatcherFactory()
        );
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
    public function shouldReturnHttpNotFoundResponseWhenReturnWatchersForNotRegisteredEvent()
    {
        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->getEventWatchers($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @return WatcherController
     */
    private function createWatcherControllerWithoutEventRegistered()
    {
        $service = new WatcherService(
            new InMemorySettingsRepository(),
            new WatcherActionHandlerFactory(),
            WatcherCounters::init()
        );

        return new WatcherController(
            $service,
            new WatcherFactory()
        );
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturnEventWatchersForInvalidEventType()
    {
        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($this->createRequest());

        $response = $controller->getEventWatchers($this->createRequestWithInvalidEventType());

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
                'event' => $invalidEventType,
                'action' => $this->watcher
            ],
            [
                'event' => $invalidEventType,
                'action' => $this->watcher
            ],
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @test
     */
    public function shouldReturnEventWatcher()
    {
        $request = $this->createRequest();

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->getEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnEventWatcherForNotRegisteredEvent()
    {
        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->getEventWatcher($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnNotRegisteredEventWatcher()
    {
        $controller = $this->createWatcherController();
        $response = $controller->getEventWatcher($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturnEventWatcherForInvalidEventType()
    {
        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($this->createRequest());

        $response = $controller->getEventWatcher($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldUnregisterEventWatcher()
    {
        $request = $this->createRequest();

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->unregisterEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterWatcherForNotRegisteredEvent()
    {
        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->unregisterEventWatcher($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterNotRegisteredEventWatcher()
    {
        $controller = $this->createWatcherController();
        $response = $controller->unregisterEventWatcher($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenUnregisterEventWatcherForInvalidEventType()
    {
        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($this->createRequest());

        $response = $controller->unregisterEventWatcher($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldRegisterEventWatcher()
    {
        $controller = $this->createWatcherController();
        $response = $controller->registerEventWatcher($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenRegisterWatcherForNotRegisteredEvent()
    {
        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->registerEventWatcher($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpConflictResponseWhenRegisterAlreadyRegisteredWatcher()
    {
        $request = $this->createRequest();

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->registerEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenRegisterEventWatcherForInvalidEventType()
    {
        $controller = $this->createWatcherController();
        $response = $controller->registerEventWatcher($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }
}
