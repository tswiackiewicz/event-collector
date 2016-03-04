<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\Exception\EventTypeAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Event\Exception\NotRegisteredCollectorsException;
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

    /**
     * @test
     */
    public function shouldCollectEvent()
    {
        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->collectEvent(
            $this->createRequest()
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return EventController
     */
    private function createEventControllerWithRegisteredCollector()
    {
        $configuration = new Configuration();
        $configuration->registerEventType(
            Event::create($this->event)
        );
        $configuration->registerEventCollector(
            $this->event,
            $this->createCollector()
        );

        return new EventController(
            $configuration
        );
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
                'target' => [
                    'type' => 'syslog',
                    'ident' => 'test'
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function shouldCollectEventWithEmptyPayload()
    {
        $payload = $this->payload;
        $this->payload = '';

        $controller = $this->createEventControllerWithRegisteredCollector();
        $response = $controller->collectEvent(
            $this->createRequest()
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_CONFLICT, $response->getStatusCode());

        $this->payload = $payload;
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenCollectingNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createEventControllerWithoutEventRegistered();
        $controller->collectEvent(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredCollectorsExceptionIfNoCollectorsRegisteredForEvent()
    {
        $this->setExpectedException(NotRegisteredCollectorsException::class);

        $controller = $this->createEventController();
        $controller->collectEvent(
            $this->createRequest()
        );
    }
}
