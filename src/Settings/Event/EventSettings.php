<?php
namespace TSwiackiewicz\EventsCollector\Settings\Event;

use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

/**
 * Interface EventSettings
 * @package TSwiackiewicz\EventsCollector\Settings\Event
 */
interface EventSettings
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
}