<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Settings\SettingsRepository;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandlerFactory;

/**
 * Class WatcherService
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class WatcherService
{
    const ACTION_MESSAGE = 'Event `%s` repeated %d times';

    /**
     * @var SettingsRepository
     */
    private $repository;

    /**
     * @var WatcherActionHandlerFactory
     */
    private $factory;

    /**
     * @var WatcherCounters
     */
    private $counters;

    /**
     * @param SettingsRepository $repository
     * @param WatcherActionHandlerFactory $factory
     * @param WatcherCounters $counters
     */
    public function __construct(
        SettingsRepository $repository,
        WatcherActionHandlerFactory $factory,
        WatcherCounters $counters
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->counters = $counters;
    }

    /**
     * @param string $eventType
     * @return Watcher[]
     */
    public function getEventWatchers($eventType)
    {
        return $this->repository->getEventWatchers($eventType);
    }

    /**
     * @param string $eventType
     * @param string $watcherName
     * @return Watcher
     */
    public function getEventWatcher($eventType, $watcherName)
    {
        return $this->repository->getEventWatcher($eventType, $watcherName);
    }

    /**
     * @param Watcher $watcher
     * @throw \Exception
     */
    public function registerEventWatcher(Watcher $watcher)
    {
        $this->repository->registerEventWatcher($watcher);
    }

    /**
     * @param string $eventType
     * @param string $watcherName
     * @throws \Exception
     */
    public function unregisterEventWatcher($eventType, $watcherName)
    {
        $this->repository->unregisterEventWatcher($eventType, $watcherName);
    }

    /**
     * @param Event $event
     */
    public function watch(Event $event)
    {
        $watchers = $event->getWatchers();
        foreach ($watchers as $watcher) {
            $watcherCounter = $this->updateWatcherCounter($watcher);
            if($watcherCounter > $watcher->getThreshold()) {
                $this->handleWatcherAction($watcher, $watcherCounter);
            }
        }
    }

    /**
     * @param Watcher $watcher
     * @return int
     */
    private function updateWatcherCounter(Watcher $watcher)
    {
        return $this->counters->increaseCounter(
            $watcher->buildAggregationKey()
        );
    }

    /**
     * @param Watcher $watcher
     * @param int $counter
     */
    private function handleWatcherAction(Watcher $watcher, $counter)
    {
        $handler = $this->factory->createFromWatcherAction($watcher->getAction());
        $handler->handle(
            sprintf(self::ACTION_MESSAGE, $watcher->getEvent(), $counter)
        );
    }
}