<?php
namespace TSwiackiewicz\EventsCollector\Event;

use TSwiackiewicz\EventsCollector\Counters\Counters;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorService;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherService;
use TSwiackiewicz\EventsCollector\Settings\Settings;

/**
 * Class EventService
 * @package TSwiackiewicz\EventsCollector\Event
 */
class EventService
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var CollectorService
     */
    private $collectorService;

    /**
     * @var WatcherService
     */
    private $watcherService;

    /**
     * @param Settings $settings
     * @param CollectorService $collectorService
     * @param WatcherService $watcherService
     */
    public function __construct(
        Settings $settings,
        CollectorService $collectorService,
        WatcherService $watcherService
    ) {
        $this->settings = $settings;
        $this->collectorService = $collectorService;
        $this->watcherService = $watcherService;
    }

    /**
     * @param Settings $settings
     * @param Counters $counters
     * @return EventService
     */
    public static function create(Settings $settings, Counters $counters)
    {
        return new static(
            $settings,
            CollectorService::create($settings),
            WatcherService::create($settings, $counters)
        );
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->settings->getEvents();
    }

    /**
     * @param string $eventType
     * @return Event
     */
    public function getEvent($eventType)
    {
        return $this->settings->getEvent($eventType);
    }

    /**
     * @param Event $event
     */
    public function registerEvent(Event $event)
    {
        $this->settings->registerEvent($event);
    }

    /**
     * @param string $eventType
     */
    public function unregisterEvent($eventType)
    {
        $this->settings->unregisterEvent($eventType);
    }

    /**
     * @param Event $event
     * @param string $eventPayload
     */
    public function collectEvent(Event $event, $eventPayload)
    {
        $this->collectorService->collect($event, $eventPayload);
        $this->watcherService->watch($event);
    }
}