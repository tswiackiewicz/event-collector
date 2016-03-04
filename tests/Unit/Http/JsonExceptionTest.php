<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Http\JsonException;

/**
 * Class JsonExceptionTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Http
 */
class JsonExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnExceptionAsJsonResponse()
    {
        $exception = new JsonException(123, 'Test exception');

        $this->assertInstanceOf(JsonResponse::class, $exception->getJsonResponse());
    }
}
