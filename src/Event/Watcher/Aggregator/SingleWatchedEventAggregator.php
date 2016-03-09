<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator;

/**
 * Class SingleWatchedEventAggregator
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator
 */
class SingleWatchedEventAggregator implements WatchedEventAggregator
{
    /**
     * @param string $eventType
     * @return string
     */
    public function buildAggregationKey($eventType)
    {
        return $eventType;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::SINGLE_AGGREGATOR
        ];
    }
}