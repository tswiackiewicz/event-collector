<?php
namespace TSwiackiewicz\EventsCollector\Event;

use TSwiackiewicz\EventsCollector\Event\Collector\CollectorService;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherService;
use TSwiackiewicz\EventsCollector\Settings\SettingsRepository;

/**
 * Class EventService
 * @package TSwiackiewicz\EventsCollector\Event
 */
class EventService
{
    /**
     * @var SettingsRepository
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
     * @param SettingsRepository $repository
     * @param CollectorService $collectorService
     * @param WatcherService $watcherService
     */
    public function __construct(
        SettingsRepository $repository,
        CollectorService $collectorService,
        WatcherService $watcherService
    ) {
        $this->repository = $repository;
        $this->collectorService = $collectorService;
        $this->watcherService = $watcherService;
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