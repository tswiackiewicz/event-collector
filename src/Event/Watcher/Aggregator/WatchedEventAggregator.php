<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator;

/**
 * Interface WatchedEventAggregator
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator
 */
interface WatchedEventAggregator
{
    const SINGLE_AGGREGATOR = 'single';
    const FIELDS_AGGREGATOR = 'fields';

    /**
     * @param string $eventType
     * @return string
     */
    public function buildAggregationKey($eventType);

    /**
     * @return array
     */
    public function toArray();
}