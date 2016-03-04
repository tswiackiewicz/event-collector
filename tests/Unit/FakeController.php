<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Controller;
use TSwiackiewicz\EventsCollector\Http\JsonException;

/**
 * Class FakeController
 * @package TSwiackiewicz\EventsCollector\Tests\Unit
 */
class FakeController extends Controller
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
     * @throws JsonException
     */
    public function throwableCallback()
    {
        throw new JsonException(
            JsonResponse::HTTP_CONFLICT,
            'Error occurs'
        );
    }
}