<?php
namespace TSwiackiewicz\EventsCollector\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JsonErrorResponse
 * @package TSwiackiewicz\EventsCollector\Http
 */
class JsonErrorResponse
{
    /**
     * @param int $statusCode
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    public static function createJsonResponse($statusCode, $message, array $headers = [])
    {
        $body = [
            'status_code' => $statusCode,
            'message' => $message
        ];

        return new JsonResponse($body, $statusCode, $headers);
    }
}