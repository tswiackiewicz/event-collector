<?php
namespace TSwiackiewicz\EventsCollector\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;
use TSwiackiewicz\EventsCollector\Collector\Exception\UnknownCollectorTypeException;
use TSwiackiewicz\EventsCollector\Collector\Syslog\SyslogCollectorTarget;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

/**
 * Class CollectorFactory
 * @package TSwiackiewicz\EventsCollector\Collector
 */
class CollectorFactory
{
    /**
     * @param string $event
     * @param string $jsonPayload
     * @return Collector
     * @throws InvalidCollectorParameterException
     * @throws UnknownCollectorTypeException
     */
    public function create($event, $jsonPayload)
    {
        $payload = RequestPayload::fromJson($jsonPayload);
        $type = $payload->getValue('target.type');

        if (empty($type)) {
            throw new InvalidCollectorParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Collector type is required');
        }

        switch ($type) {
            case SyslogCollectorTarget::SYSLOG_COLLECTOR:
                return Collector::create(
                    $payload->getValue('name'),
                    $event,
                    SyslogCollectorTarget::create(
                        $payload->getValue('target.parameters')
                    )
                );
        }

        throw new UnknownCollectorTypeException(JsonResponse::HTTP_BAD_REQUEST, 'Unknown collector type: `' . $type . '`');
    }
}
