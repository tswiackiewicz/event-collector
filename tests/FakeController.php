<?php
namespace TSwiackiewicz\EventsCollector\Tests;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class FakeController
 * @package TSwiackiewicz\EventsCollector\Tests
 */
class FakeController
{
    /**
     * @return JsonResponse
     */
    public function successfulCallback()
    {
        return new JsonResponse(
            [
                'acknowledged' => true
            ],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @return bool
     */
    public function invalidCallback()
    {
        return false;
    }

    /**
     * @throws \RuntimeException
     */
    public function throwableCallback()
    {
        throw new \RuntimeException('Error occurs');
    }
}