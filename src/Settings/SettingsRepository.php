<?php
namespace TSwiackiewicz\EventsCollector\Settings;

use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

/**
 * Interface SettingsRepository
 * @package TSwiackiewicz\EventsCollector\Settings
 */
interface SettingsRepository
{
    /**
     * @return Event[]
     */
    public function getEvents();

    /**
     * @param string $eventType
     * @return Event
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEvent($eventType);

    /**
     * @param Event $event
     * @throws AlreadyRegisteredException
     */
    public function registerEvent(Event $event);

    /**
     * @param string $eventType
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEvent($eventType);

    /**
     * @param string $eventType
     * @return Collector[]
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventCollectors($eventType);

    /**
     * @param string $eventType
     * @param string $collectorName
     * @return Collector
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventCollector($eventType, $collectorName);

    /**
     * @param Collector $collector
     * @throws AlreadyRegisteredException
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function registerEventCollector(Collector $collector);

    /**
     * @param string $eventType
     * @param string $collectorName
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEventCollector($eventType, $collectorName);

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