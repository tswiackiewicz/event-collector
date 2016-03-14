<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler;

use Psr\Log\LoggerInterface;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

/**
 * Class CollectorAppenderHandler
 * @package TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler
 */
class CollectorAppenderHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $uuid
     * @param string $payload
     */
    public function handle($uuid, $payload)
    {
        $this->logger->info($this->prepareMessage($uuid, $payload));
    }

    /**
     * @param string $uuid
     * @param string $payload
     * @return string
     * @throws InvalidParameterException
     */
    private function prepareMessage($uuid, $payload)
    {
        if (false === RequestPayload::isJsonPayload($payload)) {
            throw new InvalidParameterException('String JSON payload is expected');
        }

        $decodedPayload = json_decode($payload, true);
        $decodedPayload['event_uuid'] = $uuid;

        return json_encode($decodedPayload);
    }
}