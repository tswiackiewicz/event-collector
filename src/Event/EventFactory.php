<?php
namespace TSwiackiewicz\EventsCollector\Event;

use TSwiackiewicz\EventsCollector\Action\ActionFactory;
use TSwiackiewicz\EventsCollector\Collector\CollectorFactory;
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

        $actions = $payload->getValue('actions');
        if (is_array($actions)) {
            $actionFactory = new ActionFactory();
            foreach ($actions as $action) {
                $event->addAction(
                    $actionFactory->createFromArray($eventType, $action)
                );
            }
        }

        return $event;
    }
}