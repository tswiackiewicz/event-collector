<?php
namespace TSwiackiewicz\EventsCollector\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JsonException
 * @package TSwiackiewicz\EventsCollector\Http
 */
class JsonException extends \Exception
{
    /**
     * @var int
     */
    protected $status;

    /**
     * @var string[]
     */
    protected $headers = [];

    /**
     * @param int $status
     * @param string $message
     * @param \Exception $previous
     * @param string[] $headers
     * @param int $code
     */
    public function __construct($status, $message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->status = $status;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return JsonResponse
     */
    public function getJsonResponse()
    {
        $body = [
            'status_code' => $this->getStatusCode(),
            'message' => $this->getMessage()
        ];

        return new JsonResponse($body, $this->getStatusCode(), $this->getHeaders());
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
