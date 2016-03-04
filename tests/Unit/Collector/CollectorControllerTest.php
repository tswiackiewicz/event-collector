<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Collector\Exception\CollectorAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Collector\Exception\NotRegisteredCollectorException;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Exception\NotRegisteredEventTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Collector
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
    private $payload = '{"name":"test_collector","target":{"type":"syslog","ident":"test"}}';

    /**
     * @test
     */
    public function shouldReturnAllEventCollectorsAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getAllEventCollectors($request);

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
     * @return CollectorController
     */
    private function createCollectorController()
    {
        $configuration = new Configuration();
        $configuration->registerEventType(
            Event::create($this->event)
        );

        return new CollectorController(
            $configuration
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenReturnAllCollectorsForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $controller->getAllEventCollectors(
            $this->createRequest()
        );
    }

    /**
     * @return CollectorController
     */
    private function createCollectorControllerWithoutEventRegistered()
    {
        return new CollectorController(
            new Configuration()
        );
    }

    /**
     * @test
     */
    public function shouldReturnEventCollectorAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);

        $response = $controller->getEventCollector($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenReturnCollectorForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $controller->getEventCollector(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredCollectorExceptionWhenReturnEventCollectorForNotRegisteredCollector()
    {
        $this->setExpectedException(NotRegisteredCollectorException::class);

        $controller = $this->createCollectorController();
        $controller->getEventCollector(
            $this->createRequest()
        );
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

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenUnregisterCollectorForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $controller->unregisterEventCollector(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredCollectorExceptionWhenUnregisterEventCollectorForNotRegisteredCollector()
    {
        $this->setExpectedException(NotRegisteredCollectorException::class);

        $controller = $this->createCollectorController();
        $controller->unregisterEventCollector(
            $this->createRequest()
        );
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

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenRegisterCollectorForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createCollectorControllerWithoutEventRegistered();
        $controller->registerEventCollector(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowCollectorAlreadyRegisteredExceptionWhenRegisterAlreadyRegisteredCollector()
    {
        $this->setExpectedException(CollectorAlreadyRegisteredException::class);

        $request = $this->createRequest();

        $controller = $this->createCollectorController();
        $controller->registerEventCollector($request);
        $controller->registerEventCollector($request);
    }
}
