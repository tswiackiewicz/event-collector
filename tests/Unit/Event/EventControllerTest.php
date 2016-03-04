<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\Exception\EventTypeAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Event\Exception\NotRegisteredEventTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

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
    public function shouldReturnAllEventTypesAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->registerEventType($request);

        $response = $controller->getAllEventTypes();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
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
        $configuration = new Configuration();
        $configuration->registerEventType(
            Event::create($this->event)
        );

        return new EventController(
            $configuration
        );
    }

    /**
     * @test
     */
    public function shouldReturnEventAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $response = $controller->getEventType($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenReturnNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createEventControllerWithoutEventRegistered();
        $controller->getEventType(
            $this->createRequest()
        );
    }

    /**
     * @return EventController
     */
    private function createEventControllerWithoutEventRegistered()
    {
        return new EventController(
            new Configuration()
        );
    }

    /**
     * @test
     */
    public function shouldUnregisterEvent()
    {
        $request = $this->createRequest();

        $controller = $this->createEventController();
        $response = $controller->unregisterEventType($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenUnregisterNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createEventControllerWithoutEventRegistered();
        $controller->unregisterEventType(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldRegisterEvent()
    {
        $controller = $this->createEventController();
        $response = $controller->registerEventType(
            $this->createRequest()
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowEventTypeAlreadyRegisteredExceptionWhenRegisterAlreadyRegisteredAction()
    {
        $this->setExpectedException(EventTypeAlreadyRegisteredException::class);

        $request = $this->createRequest();

        $controller = $this->createEventController();
        $controller->registerEventType($request);
        $controller->registerEventType($request);
    }
}
