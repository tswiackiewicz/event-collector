<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherController;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\Unit\Event\ControllerBaseTestCase;

/**
 * Class WatcherControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher
 */
class WatcherControllerTest extends ControllerBaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateWatcherController()
    {
        $controller = WatcherController::create(
            new InMemorySettings(),
            new InMemoryCounters()
        );

        $this->assertInstanceOf(WatcherController::class, $controller);
    }

    /**
     * @test
     */
    public function shouldReturnEventWatchers()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->getEventWatchers($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnWatchersForNotRegisteredEvent()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->getEventWatchers($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturnEventWatchersForInvalidEventType()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->getEventWatchers(
            $this->createRequestWithInvalidEventType(['watcher' => $this->watcher])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldReturnEventWatcher()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

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
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->getEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnNotRegisteredEventWatcher()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $response = $controller->getEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturnEventWatcherForInvalidEventType()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->getEventWatcher(
            $this->createRequestWithInvalidEventType(['watcher' => $this->watcher])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldUnregisterEventWatcher()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

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
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->unregisterEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterNotRegisteredEventWatcher()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $response = $controller->unregisterEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenUnregisterEventWatcherForInvalidEventType()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $controller->registerEventWatcher($request);

        $response = $controller->unregisterEventWatcher(
            $this->createRequestWithInvalidEventType(['watcher' => $this->watcher])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldRegisterEventWatcher()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherController();
        $response = $controller->registerEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenRegisterWatcherForNotRegisteredEvent()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

        $controller = $this->createWatcherControllerWithoutEventRegistered();
        $response = $controller->registerEventWatcher($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpConflictResponseWhenRegisterAlreadyRegisteredWatcher()
    {
        $request = $this->createRequest(['watcher' => $this->watcher]);

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
        $response = $controller->registerEventWatcher(
            $this->createRequestWithInvalidEventType(['watcher' => $this->watcher])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function buildPayload()
    {
        return '{"name":"test_watcher","threshold":100,"aggregator":{"type":"single", "fields":["field_name"]},"action":{"type":"email","to":"user@domain.com","subject":"Test subject"}}';
    }
}
