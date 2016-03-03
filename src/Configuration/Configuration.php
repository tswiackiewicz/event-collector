<?php

namespace TSwiackiewicz\EventsCollector\Configuration;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;
use TSwiackiewicz\EventsCollector\Action\Action;
use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Exception\EventTypeAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Event\Exception\InvalidEventParameterException;
use TSwiackiewicz\EventsCollector\Event\Exception\NotRegisteredEventTypeException;

/**
 * Class Configuration
 * @package TSwiackiewicz\EventsCollector\Configuration
 */
class Configuration
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @param string $file
     * @return Configuration
     */
    public static function loadFromFile($file)
    {
        $configuration = new Configuration();

        $parsedConfiguration = Yaml::parse(file_get_contents($file));
        if (is_array($parsedConfiguration)) {
            foreach ($parsedConfiguration as $eventConfiguration) {
                $event = Event::createFromArray($eventConfiguration);
                $configuration->events[$event->getType()] = $event;
            }
        }

        return $configuration;
    }

    /**
     * @param string $file
     */
    public function dump($file)
    {
        $dump = [];
        foreach ($this->events as $eventType => $event) {
            $dump[] = $event->dump();
        }

        file_put_contents($file, Yaml::dump($dump));
    }

    /**
     * @return Event[]
     */
    public function getAllEventTypes()
    {
        return array_values($this->events);
    }

    /**
     * @param string $eventType
     * @throws NotRegisteredEventTypeException
     */
    public function unregisterEventType($eventType)
    {
        $this->getEventType($eventType);

        unset($this->events[$eventType]);
    }

    /**
     * @param string $eventType
     * @return Event
     * @throws InvalidEventParameterException
     * @throws NotRegisteredEventTypeException
     */
    public function getEventType($eventType)
    {
        if (empty($eventType)) {
            throw new InvalidEventParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Event type not defined');
        }

        if (empty($this->events[$eventType])) {
            throw new NotRegisteredEventTypeException(
                JsonResponse::HTTP_NOT_FOUND,
                'Event type `' . $eventType . '` is not registered'
            );
        }

        return $this->events[$eventType];
    }

    /**
     * @param Event $event
     * @throws EventTypeAlreadyRegisteredException
     */
    public function registerEventType(Event $event)
    {
        $eventType = $event->getType();

        if (!empty($this->events[$eventType])) {
            throw new EventTypeAlreadyRegisteredException(
                JsonResponse::HTTP_CONFLICT,
                'Event type `' . $eventType . '` already registered'
            );
        }

        $this->events[$eventType] = $event;
    }

    /**
     * @param $eventType
     * @return Collector[]
     */
    public function getEventCollectors($eventType)
    {
        $event = $this->getEventType($eventType);

        return $event->getCollectors();
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     * @return Collector
     */
    public function getEventCollector($eventType, $collectorName)
    {
        $event = $this->getEventType($eventType);

        return $event->getCollector($collectorName);
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     */
    public function unregisterEventCollector($eventType, $collectorName)
    {
        $event = $this->getEventType($eventType);
        $event->removeCollector($collectorName);

        $this->events[$eventType] = $event;
    }

    /**
     * @param $eventType
     * @param Collector $collector
     */
    public function registerEventCollector($eventType, Collector $collector)
    {
        $event = $this->getEventType($eventType);
        $event->addCollector($collector);

        $this->events[$eventType] = $event;
    }

    /**
     * @param $eventType
     * @return Action[]
     */
    public function getEventActions($eventType)
    {
        $event = $this->getEventType($eventType);

        return $event->getActions();
    }

    /**
     * @param string $eventType
     * @param string $actionName
     * @return Action
     */
    public function getEventAction($eventType, $actionName)
    {
        $event = $this->getEventType($eventType);

        return $event->getAction($actionName);
    }

    /**
     * @param string $eventType
     * @param string $actionName
     */
    public function unregisterEventAction($eventType, $actionName)
    {
        $event = $this->getEventType($eventType);
        $event->removeAction($actionName);

        $this->events[$eventType] = $event;
    }

    /**
     * @param string $eventType
     * @param Action $action
     */
    public function registerEventAction($eventType, Action $action)
    {
        $event = $this->getEventType($eventType);
        $event->addAction($action);

        $this->events[$eventType] = $event;
    }
}