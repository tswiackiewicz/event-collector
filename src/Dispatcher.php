<?php

namespace TSwiackiewicz\EventsCollector;

use FastRoute\Dispatcher as FastRouteDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Http\JsonException;

/**
 * Class Dispatcher
 * @package TSwiackiewicz\EventsCollector
 */
class Dispatcher
{
    /**
     * @var FastRouteDispatcher
     */
    private $baseDispatcher;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param FastRouteDispatcher $baseDispatcher
     * @param Configuration $configuration
     */
    public function __construct(FastRouteDispatcher $baseDispatcher, Configuration $configuration)
    {
        $this->baseDispatcher = $baseDispatcher;
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $payload
     * @return JsonResponse
     */
    public function dispatch($method, $uri, $payload)
    {
        $match = $this->baseDispatcher->dispatch($method, $uri);

        if (FastRouteDispatcher::NOT_FOUND === $match[0]) {
            return $this->handleNotFound();
        }

        if (FastRouteDispatcher::METHOD_NOT_ALLOWED === $match[0]) {
            return $this->handleNotAllowed();
        }

        return $this->handleFound($match[1], $method, $uri, $match[2], $payload);
    }

    /**
     * @return JsonResponse
     */
    private function handleNotFound()
    {
        return (new JsonException(JsonResponse::HTTP_NOT_FOUND, 'Not Found'))->getJsonResponse();
    }

    /**
     * @return JsonResponse
     */
    private function handleNotAllowed()
    {
        return (new JsonException(JsonResponse::HTTP_METHOD_NOT_ALLOWED, 'Method Not Allowed'))->getJsonResponse();
    }

    /**
     * @param string[] $controller
     * @param string $method
     * @param string $uri
     * @param string $query
     * @param string $payload
     * @return JsonResponse
     */
    private function handleFound(array $controller, $method, $uri, $query, $payload)
    {
        try {
            $response = $this->invokeController(
                $controller,
                [
                    $this->buildRequest($method, $uri, $query, $payload)
                ]
            );

            if ($response instanceof JsonResponse) {
                return $response;
            }

            throw new JsonException(
                JsonResponse::HTTP_CONFLICT,
                'Defined controller action must return Symfony\Component\HttpFoundation\JsonResponse'
            );
        } catch (JsonException $e) {
            return $e->getJsonResponse();
        }
    }

    /**
     * @param string[] $controller
     * @param array $attributes
     * @return mixed
     * @throws JsonException
     */
    private function invokeController(array $controller, array $attributes = [])
    {
        if (count($controller) != 2) {
            throw new JsonException(
                JsonResponse::HTTP_CONFLICT,
                'Defined controller must contain class name and callback method name'
            );
        }

        return call_user_func_array(
            [
                new $controller[0]($this->configuration),
                $controller[1]
            ],
            array_values($attributes)
        );
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $query
     * @param string $payload
     * @return Request
     */
    private function buildRequest($method, $uri, $query, $payload)
    {
        return Request::create(
            $uri,
            $method,
            $query,
            [],
            [],
            [],
            $payload
        );
    }
}
