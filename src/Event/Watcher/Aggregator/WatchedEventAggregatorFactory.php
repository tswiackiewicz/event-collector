<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

/**
 * Class WatchedEventAggregatorFactory
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator
 */
class WatchedEventAggregatorFactory
{
    /**
     * @param array $aggregatorConfiguration
     * @return WatchedEventAggregator
     * @throws InvalidParameterException
     * @throws UnknownTypeException
     */
    public function create(array $aggregatorConfiguration)
    {
        $payload = RequestPayload::fromJson(json_encode($aggregatorConfiguration));

        $aggregatorType = $payload->getValue('type', '');
        if (empty($aggregatorType)) {
            throw new InvalidParameterException('Watcher aggregator.type is required');
        }

        switch ($aggregatorType) {
            case WatchedEventAggregator::SINGLE_AGGREGATOR:
                return new SingleWatchedEventAggregator();

            case WatchedEventAggregator::FIELDS_AGGREGATOR:
                return new FieldsBasedWatchedEventAggregator(
                    $payload->getValue('fields', [])
                );
        }

        throw new UnknownTypeException('Unknown watcher aggregator.type: `' . $aggregatorType . '`');
    }
}