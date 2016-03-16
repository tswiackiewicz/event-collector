<?php
namespace TSwiackiewicz\EventsCollector\Event;

use TSwiackiewicz\EventsCollector\Event\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherFactory;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class EventFactory
 * @package TSwiackiewicz\EventsCollector\Event
 */
class EventFactory
{
    /**
     * @param array $eventConfiguration
     * @return Event
     */
    public function createFromArray(array $eventConfiguration)
    {
        $payload = RequestPayload::fromJson(
            json_encode($eventConfiguration)
        );
        $eventType = $payload->getValue('type');

        $event = new Event(
            new Uuid($payload->getValue('_id')),
            $eventType
        );

        $collectors = $payload->getValue('collectors');
        if (is_array($collectors)) {
            $collectorFactory = new CollectorFactory();
            foreach ($collectors as $collector) {
                $event->addCollector(
                    $collectorFactory->createFromArray($eventType, $collector)
                );
            }
        }

        $watchers = $payload->getValue('watchers');
        if (is_array($watchers)) {
            $watcherFactory = new WatcherFactory();
            foreach ($watchers as $watcher) {
                $event->addWatcher(
                    $watcherFactory->createFromArray($eventType, $watcher)
                );
            }
        }

        return $event;
    }
}