<?php
namespace TSwiackiewicz\EventsCollector\Settings;

use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

/**
 * Class InMemorySettingsRepository
 * @package TSwiackiewicz\EventsCollector\Settings
 */
class InMemorySettingsRepository implements SettingsRepository
{
    /**
     * @var array [event_type => Event]
     */
    private $events = [];

    /**
     * @param array $events
     */
    public function __construct(array $events = [])
    {
        $this->events = $events;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return array_values($this->events);
    }

    /**
     * @param Event $event
     * @throws InvalidParameterException
     * @throws AlreadyRegisteredException
     */
    public function registerEvent(Event $event)
    {
        $eventType = $event->getType();

        if(empty($eventType)) {
            throw new InvalidParameterException('Event type not defined');
        }

        if(!empty($this->events[$eventType])) {
            throw new AlreadyRegisteredException('Event type `' . $eventType . '` already registered');
        }

        $this->events[$eventType] = $event;
    }

    /**
     * @param string $eventType
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEvent($eventType)
    {
        $this->getEvent($eventType);

        unset($this->events[$eventType]);
    }

    /**
     * @param string $eventType
     * @return Event
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEvent($eventType)
    {
        if(empty($eventType)) {
            throw new InvalidParameterException('Event type not defined');
        }

        if(empty($this->events[$eventType])) {
            throw new NotRegisteredException('Event type `' . $eventType . '` is not registered');
        }

        return $this->events[$eventType];
    }

    /**
     * @param string $eventType
     * @return Collector[]
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventCollectors($eventType)
    {
        $event = $this->getEvent($eventType);

        return $event->getCollectors();
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     * @return Collector
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventCollector($eventType, $collectorName)
    {
        $event = $this->getEvent($eventType);

        return $event->getCollector($collectorName);
    }

    /**
     * @param Collector $collector
     * @throws AlreadyRegisteredException
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function registerEventCollector(Collector $collector)
    {
        $eventType = $collector->getEvent();

        $event = $this->getEvent($eventType);
        $event->addCollector($collector);

        $this->events[$eventType] = $event;
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEventCollector($eventType, $collectorName)
    {
        $event = $this->getEvent($eventType);
        $event->removeCollector($collectorName);

        $this->events[$eventType] = $event;
    }

    /**
     * @param string $eventType
     * @return Watcher[]
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventWatchers($eventType)
    {
        $event = $this->getEvent($eventType);

        return $event->getWatchers();
    }

    /**
     * @param string $eventType
     * @param string $watcherName
     * @return Watcher
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventWatcher($eventType, $watcherName)
    {
        $event = $this->getEvent($eventType);

        return $event->getWatcher($watcherName);
    }

    /**
     * @param Watcher $watcher
     * @throws AlreadyRegisteredException
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function registerEventWatcher(Watcher $watcher)
    {
        $eventType = $watcher->getEvent();

        $event = $this->getEvent($eventType);
        $event->addWatcher($watcher);

        $this->events[$eventType] = $event;
    }

    /**
     * @param string $eventType
     * @param string $watcherName
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEventWatcher($eventType, $watcherName)
    {
        $event = $this->getEvent($eventType);
        $event->removeWatcher($watcherName);

        $this->events[$eventType] = $event;
    }
}