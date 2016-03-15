<?php

namespace TSwiackiewicz\EventsCollector;

use FastRoute\Dispatcher as FastRouteDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Exception\InvalidControllerDefinitionException;
use TSwiackiewicz\EventsCollector\Http\JsonErrorResponse;

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
     * @var ControllerFactory
     */
    private $factory;

    /**
     * @param FastRouteDispatcher $baseDispatcher
     * @param ControllerFactory $factory
     */
    public function __construct(FastRouteDispatcher $baseDispatcher, ControllerFactory $factory)
    {
        $this->baseDispatcher = $baseDispatcher;
        $this->factory = $factory;
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
        return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, 'Not Found');
    }

    /**
     * @return JsonResponse
     */
    private function handleNotAllowed()
    {
        return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_METHOD_NOT_ALLOWED, 'Method Not Allowed');
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

            throw new InvalidControllerDefinitionException(
                'Defined controller action must return Symfony\Component\HttpFoundation\JsonResponse'
            );
        } catch (InvalidControllerDefinitionException $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_CONFLICT, $e->getMessage());
        }
    }

    /**
     * @param string[] $controller
     * @param array $attributes
     * @return mixed
     * @throws InvalidControllerDefinitionException
     */
    private function invokeController(array $controller, array $attributes = [])
    {
        if (count($controller) != 2) {
            throw new InvalidControllerDefinitionException(
                'Defined controller must contain class name and callback method name'
            );
        }

        return call_user_func_array(
            [
                $this->factory->create($controller[0]),
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

    /**
     * @param string $file
     */
    public function dumpSettings($file)
    {
        $this->factory->dumpSettings($file);
    }
}
