<?php
namespace TSwiackiewicz\EventsCollector\Collector\Handler;

use Psr\Log\LoggerInterface;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class CollectorHandler
 * @package TSwiackiewicz\EventsCollector\Collector\Handler
 */
class CollectorHandler
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
     * @param Uuid $uuid
     * @param string $payload
     */
    public function handle(Uuid $uuid, $payload)
    {
        $this->logger->info($this->prepareMessage($uuid, $payload));
    }

    /**
     * @param Uuid $uuid
     * @param string $payload
     * @return string
     * @throws \RuntimeException
     */
    private function prepareMessage(Uuid $uuid, $payload)
    {
        if (empty($payload) || !is_string($payload)) {
            throw new \RuntimeException('String JSON payload is expected');
        }

        $decodedPayload = json_decode($payload, true);
        $decodedPayload['event_uuid'] = $uuid->getUuid();

        return json_encode($decodedPayload);
    }
}