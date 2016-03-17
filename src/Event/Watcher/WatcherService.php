<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use TSwiackiewicz\EventsCollector\Counters\Counters;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandlerFactory;
use TSwiackiewicz\EventsCollector\Settings\Settings;

/**
 * Class WatcherService
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class WatcherService
{
    const ACTION_MESSAGE = 'Event `%s` repeated %d times';

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var WatcherActionHandlerFactory
     */
    private $factory;

    /**
     * @var Counters
     */
    private $counters;

    /**
     * @param Settings $settings
     * @param WatcherActionHandlerFactory $factory
     * @param Counters $counters
     */
    public function __construct(
        Settings $settings,
        WatcherActionHandlerFactory $factory,
        Counters $counters
    ) {
        $this->settings = $settings;
        $this->factory = $factory;
        $this->counters = $counters;
    }

    /**
     * @param Settings $settings
     * @param Counters $counters
     * @return WatcherService
     */
    public static function create(Settings $settings, Counters $counters)
    {
        return new static(
            $settings,
            new WatcherActionHandlerFactory(),
            $counters
        );
    }

    /**
     * @param string $eventType
     * @return Watcher[]
     */
    public function getEventWatchers($eventType)
    {
        return $this->settings->getEventWatchers($eventType);
    }

    /**
     * @param string $eventType
     * @param string $watcherName
     * @return Watcher
     */
    public function getEventWatcher($eventType, $watcherName)
    {
        return $this->settings->getEventWatcher($eventType, $watcherName);
    }

    /**
     * @param Watcher $watcher
     * @throw \Exception
     */
    public function registerEventWatcher(Watcher $watcher)
    {
        $this->settings->registerEventWatcher($watcher);
    }

    /**
     * @param string $eventType
     * @param string $watcherName
     * @throws \Exception
     */
    public function unregisterEventWatcher($eventType, $watcherName)
    {
        $this->settings->unregisterEventWatcher($eventType, $watcherName);
    }

    /**
     * @param Event $event
     */
    public function watch(Event $event)
    {
        $watchers = $event->getWatchers();
        foreach ($watchers as $watcher) {
            $watcherCounter = $this->updateWatcherCounter($watcher);
            if ($watcherCounter > $watcher->getThreshold()) {
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

        $this->counters->initCounter($watcher->buildAggregationKey(), 1);
    }
}