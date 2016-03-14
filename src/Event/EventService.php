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
    private $repository;

    /**
     * @var CollectorService
     */
    private $collectorService;

    /**
     * @var WatcherService
     */
    private $watcherService;

    /**
     * @param Settings $repository
     * @param CollectorService $collectorService
     * @param WatcherService $watcherService
     */
    public function __construct(
        Settings $repository,
        CollectorService $collectorService,
        WatcherService $watcherService
    ) {
        $this->repository = $repository;
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
        return $this->repository->getEvents();
    }

    /**
     * @param string $eventType
     * @return Event
     */
    public function getEvent($eventType)
    {
        return $this->repository->getEvent($eventType);
    }

    /**
     * @param Event $event
     */
    public function registerEvent(Event $event)
    {
        $this->repository->registerEvent($event);
    }

    /**
     * @param string $eventType
     */
    public function unregisterEvent($eventType)
    {
        $this->repository->unregisterEvent($eventType);
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