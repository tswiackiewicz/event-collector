<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherEmailAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\FieldsBasedWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class WatcherFactory
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class WatcherFactory
{
    /**
     * @param string $eventType
     * @param string $jsonPayload
     * @return Watcher
     * @throws InvalidParameterException
     * @throws UnknownTypeException
     */
    public function create($eventType, $jsonPayload)
    {
        $payload = RequestPayload::fromJson($jsonPayload);

        $actionType = $payload->getValue('action.type');
        if (empty($actionType)) {
            throw new InvalidParameterException('Watcher action.type is required');
        }

        $aggregatorType = $payload->getValue('aggregator.type');
        if (empty($aggregatorType)) {
            throw new InvalidParameterException('Watcher aggregator.type is required');
        }

        switch ($actionType) {
            case WatcherEmailAction::EMAIL_ACTION:
                return Watcher::create(
                    $payload->getValue('name', ''),
                    $eventType,
                    $payload->getValue('threshold', 0),
                    new FieldsBasedWatchedEventAggregator(
                        $payload->getValue('aggregator.fields', [])
                    ),
                    WatcherEmailAction::create(
                        $payload->getValue('action')
                    )
                );
        }

        throw new UnknownTypeException('Unknown watcher action.type: `' . $actionType . '`');
    }

    /**
     * @param string $eventType
     * @param array $actionConfiguration
     * @return Watcher
     * @throws InvalidParameterException
     * @throws UnknownTypeException
     */
    public function createFromArray($eventType, array $actionConfiguration)
    {
        $payload = RequestPayload::fromJson(json_encode($actionConfiguration));

        $actionType = $payload->getValue('action.type');
        if (empty($actionType)) {
            throw new InvalidParameterException('Watcher action.type is required');
        }

        $aggregatorType = $payload->getValue('aggregator.type');
        if (empty($aggregatorType)) {
            throw new InvalidParameterException('Watcher aggregator.type is required');
        }

        switch ($actionType) {
            case WatcherEmailAction::EMAIL_ACTION:
                return new Watcher(
                    new Uuid($payload->getValue('_id')),
                    $payload->getValue('name', ''),
                    $eventType,
                    $payload->getValue('threshold', 0),
                    new FieldsBasedWatchedEventAggregator(
                        $payload->getValue('aggregator.fields', [])
                    ),
                    WatcherEmailAction::create(
                        $payload->getValue('action')
                    )
                );
        }

        throw new UnknownTypeException('Unknown watcher action.type: `' . $actionType . '`');
    }
}