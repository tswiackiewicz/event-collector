<?php
namespace TSwiackiewicz\EventsCollector\Settings\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

/**
 * Interface WatcherSettings
 * @package TSwiackiewicz\EventsCollector\Settings\Event\Watcher
 */
interface WatcherSettings
{
    /**
     * @param string $eventType
     * @return Watcher[]
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventWatchers($eventType);

    /**
     * @param string $eventType
     * @param string $watcherName
     * @return Watcher
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventWatcher($eventType, $watcherName);

    /**
     * @param Watcher $watcher
     * @throws AlreadyRegisteredException
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function registerEventWatcher(Watcher $watcher);

    /**
     * @param string $eventType
     * @param string $watcherName
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEventWatcher($eventType, $watcherName);
}