<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorSyslogAppender;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class CollectorFactory
 * @package TSwiackiewicz\EventsCollector\Event\Collector
 */
class CollectorFactory
{
    /**
     * @param string $eventType
     * @param string $jsonPayload
     * @return Collector
     * @throws InvalidParameterException
     * @throws UnknownTypeException
     */
    public function create($eventType, $jsonPayload)
    {
        $payload = RequestPayload::fromJson($jsonPayload);
        $appenderType = $payload->getValue('appender.type');

        if(empty($appenderType)) {
            throw new InvalidParameterException('Collector appender type is required');
        }

        switch ($appenderType) {
            case CollectorSyslogAppender::SYSLOG_APPENDER:
                return Collector::create(
                    $payload->getValue('name'),
                    $eventType,
                    CollectorSyslogAppender::create(
                        $payload->getValue('appender')
                    )
                );
        }

        throw new UnknownTypeException('Unknown collector appender type: `' . $appenderType . '`');
    }

    /**
     * @param string $eventType
     * @param array $collectorConfiguration
     * @return Collector
     * @throws InvalidParameterException
     * @throws UnknownTypeException
     */
    public function createFromArray($eventType, array $collectorConfiguration)
    {
        $payload = RequestPayload::fromJson(
            json_encode($collectorConfiguration)
        );
        $appenderType = $payload->getValue('appender.type');

        if(empty($appenderType)) {
            throw new InvalidParameterException('Collector appender type is required');
        }

        switch ($appenderType) {
            case CollectorSyslogAppender::SYSLOG_APPENDER:
                return new Collector(
                    new Uuid($payload->getValue('_id')),
                    $payload->getValue('name'),
                    $eventType,
                    CollectorSyslogAppender::create(
                        $payload->getValue('appender')
                    )
                );
        }

        throw new UnknownTypeException('Unknown collector appender type: `' . $appenderType . '`');
    }
}
