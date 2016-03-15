<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\Unit\Event\ControllerBaseTestCase;

/**
 * Class CollectorControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector
 */
class CollectorControllerTest extends ControllerBaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateController()
    {
        $controller = CollectorController::create(new InMemorySettings());

        $this->assertInstanceOf(CollectorController::class, $controller);
    }

    /**
     * @test
     */
    public function shouldReturnEventCollectors()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getEventCollectors($request);

        $this->assertResponse($response, JsonResponse::HTTP_OK);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturnCollectorsForNotRegisteredEvent()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->getEventCollectors($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturnCollectorsForInvalidEventType()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getEventCollectors(
            $this->createRequestWithInvalidEventType(['collector' => $this->collector])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldReturnEventCollector()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

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
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->getEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenReturningNotRegisteredEventCollector()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $response = $controller->getEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenReturningCollectorForInvalidEventType()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getEventCollector(
            $this->createRequestWithInvalidEventType(['collector' => $this->collector])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldUnregisterEventCollector()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

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
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->unregisterEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpNotFoundResponseWhenUnregisterNotRegisteredEventCollector()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $response = $controller->unregisterEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpBadRequestResponseWhenUnregisterCollectorForInvalidEventType()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->unregisterEventCollector(
            $this->createRequestWithInvalidEventType(['collector' => $this->collector])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function shouldRegisterEventCollector()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $response = $controller->registerEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function shouldReturnNotFoundResponseWhenRegisterCollectorForNotRegisteredEvent()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $response = $controller->registerEventCollector($request);

        $this->assertResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldReturnHttpConflictResponseWhenRegisterAlreadyRegisteredCollector()
    {
        $request = $this->createRequest(['collector' => $this->collector]);

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
        $request = $this->createRequest(['collector' => $this->collector]);

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->registerEventCollector(
            $this->createRequestWithInvalidEventType(['collector' => $this->collector])
        );

        $this->assertResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @return string
     */
    protected function buildPayload()
    {
        return '{"name":"test_collector","appender":{"type":"syslog","ident":"test"}}';
    }
}
