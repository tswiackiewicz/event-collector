<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\FieldsBasedWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Exception\InvalidControllerDefinitionException;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;

/**
 * Class EventControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event
 */
class EventControllerTest extends ControllerBaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateEventController()
    {
        $controller = EventController::create(
            new InMemorySettings(),
            new InMemoryCounters()
        );

        $this->assertInstanceOf(EventController::class, $controller);
    }

    /**
     * @test
     */
    public function shouldReturnAllRegisteredEventsAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->invoke('registerEvent', $request);

        $response = $controller->invoke('getEvents', $request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $response = $controller->invoke('getEvent', $request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnNotRegisteredEvent()
    {
        $controller = $this->createEventControllerWithoutEventRegistered();
        $response = $controller->invoke('getEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestResponseWhenReturningEventWithInvalidEventType()
    {
        $controller = $this->createEventController();
        $response = $controller->invoke('getEvent', $this->createRequestWithInvalidEventType([], '{"type": []}'));

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldUnregisterEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $response = $controller->invoke('unregisterEvent', $request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterNotRegisteredEvent()
    {
        $controller = $this->createEventControllerWithoutEventRegistered();
        $response = $controller->invoke('unregisterEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestResponseWhenUnregisterEventWithInvalidEventType()
    {
        $controller = $this->createEventController();
        $response = $controller->invoke('unregisterEvent',
            $this->createRequestWithInvalidEventType([], '{"type": []}'));

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldRegisterEvent()
    {
        $controller = $this->createEventController();
        $response = $controller->invoke('registerEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function shouldReturnConflictResponseWhenRegisterAlreadyRegisteredEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->invoke('registerEvent', $request);

        $response = $controller->invoke('registerEvent', $request);

        $this->assertResponse($response, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestResponseWhenRegisterEventWithInvalidEventType()
    {
        $controller = $this->createEventController();
        $response = $controller->invoke('registerEvent', $this->createRequestWithInvalidEventType([], '{"type": []}'));

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldCollectEvent()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->invoke('collectEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_OK);
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
        $response = $controller->invoke('collectEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenCollectingNotRegisteredEvent()
    {
        $controller = $this->createEventControllerWithoutEventRegistered();
        $response = $controller->invoke('collectEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenNoCollectorsRegisteredForEvent()
    {
        $controller = $this->createEventController();
        $response = $controller->invoke('collectEvent', $this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenCollectingEventWithInvalidType()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->invoke('collectEvent', $this->createRequestWithInvalidEventType([], '{"type": []}'));

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenCollectingEventWithEmptyPayload()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->invoke('collectEvent', $this->createRequestWithEmptyPayload());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidControllerDefinitionExceptionWhenInvokedMethodIsNotDefined()
    {
        $this->setExpectedException(InvalidControllerDefinitionException::class);

        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->invoke('notDefinedMethod', $request);
    }

    /**
     * @return string
     */
    protected function buildPayload()
    {
        return '{"type":"test_event"}';
    }
}
