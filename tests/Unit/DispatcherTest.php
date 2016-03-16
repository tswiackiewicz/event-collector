<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit;

use FastRoute\DataGenerator\GroupCountBased as FastRouteDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as FastRouteGroupCountBasedDispatcher;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std as FastRouteStdRouteParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Dispatcher;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;
use TSwiackiewicz\EventsCollector\Tests\FakeController;
use TSwiackiewicz\EventsCollector\Tests\FakeControllerFactory;

/**
 * Class DispatcherTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit
 */
class DispatcherTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldDispatchSuccessfully()
    {
        $dispatcher = $this->createDispatcher();
        $response = $dispatcher->dispatch('GET', '/success/', '');

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @return Dispatcher
     */
    private function createDispatcher()
    {
        $routes = $this->createRoutesCollection();

        return new Dispatcher(
            new FastRouteGroupCountBasedDispatcher(
                $routes->getRoutes()
            ),
            new FakeControllerFactory(
                new InMemorySettings(),
                new InMemoryCounters()
            )
        );
    }

    /**
     * @return RoutesCollection
     */
    private function createRoutesCollection()
    {
        $routes = new FastRouteCollector(
            new FastRouteStdRouteParser(),
            new FastRouteDataGenerator()
        );

        $routes->addRoute(
            'GET',
            '/success/',
            [FakeController::class, 'successfulCallback']
        );
        $routes->addRoute(
            'PUT',
            '/invalid_callback_response/',
            [FakeController::class, 'invalidCallback']
        );
        $routes->addRoute(
            'GET',
            '/invalid_controller/',
            ['throwableCallback']
        );
        $routes->addRoute(
            'POST',
            '/invalid_json_payload/',
            [FakeController::class, 'successfulCallback']
        );

        return new RoutesCollection($routes);
    }

    /**
     * @test
     */
    public function shouldDispatchWithInvalidCallbackResponse()
    {
        $dispatcher = $this->createDispatcher();
        $response = $dispatcher->dispatch('PUT', '/invalid_callback_response/', '');

        $this->assertResponseStatusCode($response, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @param JsonResponse $response
     * @param $statusCode
     */
    private function assertResponseStatusCode(JsonResponse $response, $statusCode)
    {
        $content = $response->getContent();
        $decodedResponse = json_decode($content, true);

        $this->assertEquals($statusCode, $decodedResponse['status_code']);
    }

    /**
     * @test
     */
    public function shouldDispatchNotAllowedMethod()
    {
        $dispatcher = $this->createDispatcher();
        $response = $dispatcher->dispatch('POST', '/success/', '');

        $this->assertResponseStatusCode($response, JsonResponse::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @test
     */
    public function shouldDispatchNotFoundUri()
    {
        $dispatcher = $this->createDispatcher();
        $response = $dispatcher->dispatch('GET', '/not_found/', '');

        $this->assertResponseStatusCode($response, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function shouldDispatchWithInvalidController()
    {
        $dispatcher = $this->createDispatcher();
        $response = $dispatcher->dispatch('GET', '/invalid_controller/', '');

        $this->assertResponseStatusCode($response, JsonResponse::HTTP_CONFLICT);
    }

    /**
     * @test
     */
    public function shouldDispatchRequestWithInvalidPayload()
    {
        $dispatcher = $this->createDispatcher();
        $response = $dispatcher->dispatch('POST', '/invalid_json_payload/',
            'payload=invalid_json_payload&error_expected=true');

        $this->assertResponseStatusCode($response, JsonResponse::HTTP_BAD_REQUEST);
    }
}
