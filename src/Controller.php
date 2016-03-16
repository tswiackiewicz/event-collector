<?php
namespace TSwiackiewicz\EventsCollector;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Exception\InvalidControllerDefinitionException;

/**
 * Interface Controller
 * @package TSwiackiewicz\EventsCollector
 */
interface Controller
{
    /**
     * @param string $method
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidControllerDefinitionException
     */
    public function invoke($method, Request $request);
}