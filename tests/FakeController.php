<?php
namespace TSwiackiewicz\EventsCollector\Tests;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Controller;

/**
 * Class FakeController
 * @package TSwiackiewicz\EventsCollector\Tests
 */
class FakeController implements Controller
{
    /**
     * {@inheritdoc}
     */
    public function invoke($method, Request $request)
    {
        switch ($method)
        {
            case 'successfulCallback':
                return $this->successfulCallback();

            case 'invalidCallback':
                return $this->invalidCallback();

            case 'throwableCallback':
                return $this->throwableCallback();
        }

        throw new \RuntimeException();
    }


    /**
     * @return JsonResponse
     */
    private function successfulCallback()
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
    private function invalidCallback()
    {
        return false;
    }

    /**
     * @throws \RuntimeException
     */
    private function throwableCallback()
    {
        throw new \RuntimeException('Error occurs');
    }
}