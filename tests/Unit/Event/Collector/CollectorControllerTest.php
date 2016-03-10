<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorService;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettingsRepository;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector
 */
class CollectorControllerTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $event = 'test_event';

    /**
     * @var string
     */
    private $collector = 'test_collector';

    /**
     * @var string
     */
    private $payload = '{"name":"test_collector","appender":{"type":"syslog","ident":"test"}}';

    /**
     * @test
     */
    public function shouldReturnEventCollectors()
    {
        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getEventCollectors($request);

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
                'collector' => $this->collector
            ],
            [
                'event' => $this->event,
                'collector' => $this->collector
            ],
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @return Request
     */
    private function createRequestWithInvalidEventType()
    {
        return new Request(
            [
                'event' => [],
                'collector' => $this->collector
            ],
            [
                'event' => [],
                'collector' => $this->collector
            ],
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @return CollectorController
     */
    private function createCollectorController()
    {
        $events[$this->event] = Event::create($this->event);
        $service = new CollectorService(
            new InMemorySettingsRepository($events),
            new CollectorAppenderHandlerFactory()
        );

        return new CollectorController($service, new CollectorFactory());
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
    public function shouldReturnHttpNotFoundResponseWhenReturnCollectorsForNotRegisteredEvent()
    {
        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->getEventCollectors($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturnCollectorsForInvalidEventType()
    {
        $controller = $this->createCollectorController();
        $controller->registerEventCollector($this->createRequest());

        $response = $controller->getEventCollectors($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return CollectorController
     */
    private function createCollectorControllerWithoutEventRegistered()
    {
        $service = new CollectorService(
            new InMemorySettingsRepository(),
            new CollectorAppenderHandlerFactory()
        );

        return new CollectorController($service, new CollectorFactory());
    }

    /**
     * @test
     */
    public function shouldReturnEventCollector()
    {
        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturningEventCollectorForNotRegisteredEvent()
    {
        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->getEventCollector($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturningNotRegisteredEventCollector()
    {
        $controller = $this->createCollectorController();
        $response = $controller->getEventCollector($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturningCollectorForInvalidEventType()
    {
        $controller = $this->createCollectorController();
        $controller->registerEventCollector($this->createRequest());

        $response = $controller->getEventCollector($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldUnregisterEventCollector()
    {
        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->unregisterEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterCollectorForNotRegisteredEvent()
    {
        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->unregisterEventCollector($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterNotRegisteredEventCollector()
    {
        $controller = $this->createCollectorController();
        $response = $controller->unregisterEventCollector($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenUnregisterCollectorForInvalidEventType()
    {
        $controller = $this->createCollectorController();
        $controller->registerEventCollector($this->createRequest());

        $response = $controller->unregisterEventCollector($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldRegisterEventCollector()
    {
        $controller = $this->createCollectorController();
        $response = $controller->registerEventCollector(
            $this->createRequest()
        );

        $this->assertResponse($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function shouldReturnNotFoundResponseWhenRegisterCollectorForNotRegisteredEvent()
    {
        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->registerEventCollector($this->createRequest());

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpConflictResponseWhenRegisterAlreadyRegisteredCollector()
    {
        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->registerEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenRegisterCollectorForInvalidEventType()
    {
        $controller = $this->createCollectorController();
        $controller->registerEventCollector($this->createRequest());

        $response = $controller->registerEventCollector($this->createRequestWithInvalidEventType());

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }
}
