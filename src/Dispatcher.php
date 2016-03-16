<?php

namespace TSwiackiewicz\EventsCollector;

use FastRoute\Dispatcher as FastRouteDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Exception\InvalidControllerDefinitionException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Http\JsonErrorResponse;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

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
     * @param string[] $controllerDefinition
     * @param string $method
     * @param string $uri
     * @param string $query
     * @param string $payload
     * @return JsonResponse
     */
    private function handleFound(array $controllerDefinition, $method, $uri, $query, $payload)
    {
        try {
            $response = $this->invokeController(
                $controllerDefinition,
                $this->buildRequest($method, $uri, $query, $payload)
            );

            if ($response instanceof JsonResponse) {
                return $response;
            }

            throw new InvalidControllerDefinitionException(
                'Defined controller action must return Symfony\Component\HttpFoundation\JsonResponse'
            );
        } catch (InvalidControllerDefinitionException $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (InvalidParameterException $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * @param string[] $controllerDefinition
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidControllerDefinitionException
     */
    private function invokeController(array $controllerDefinition, Request $request)
    {
        if (count($controllerDefinition) != 2) {
            throw new InvalidControllerDefinitionException(
                'Defined controller must contain class name and callback method name'
            );
        }

        /** @var Controller $controller */
        $controller = $this->factory->create($controllerDefinition[0]);

        return $controller->invoke($controllerDefinition[1], $request);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $query
     * @param string $payload
     * @return Request
     * @throws InvalidParameterException
     */
    private function buildRequest($method, $uri, $query, $payload)
    {
        if (!empty($payload) && false === RequestPayload::isJsonPayload($payload)) {
            throw new InvalidParameterException('Only string JSON payload is accepted');
        }

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
