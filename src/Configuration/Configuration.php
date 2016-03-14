<?php

namespace TSwiackiewicz\EventsCollector\Configuration;

use Symfony\Component\Yaml\Yaml;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventFactory;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

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
    public static function loadFromFile($file = '')
    {
        $configuration = new Configuration();
        $parsedConfiguration = $configuration->getParsedConfiguration($file);

        $factory = new EventFactory();
        foreach ($parsedConfiguration as $eventConfiguration) {
            $event = $factory->createFromArray($eventConfiguration);
            $configuration->events[$event->getType()] = $event;
        }

        return $configuration;
    }

    /**
     * @param string $file
     * @return array
     */
    private function getParsedConfiguration($file = '')
    {
        $configFile = $file;
        if (empty($configFile)) {
            $configDumpPath = getenv('CONFIGURATION_DUMP_FILE_PATH');
            $configFilePath = getenv('CONFIGURATION_FILE_PATH');

            if (is_readable($configDumpPath)) {
                $configFile = $configDumpPath;
            } else if (is_readable($configFilePath)) {
                $configFile = $configFilePath;
            }
        }

        $parsedConfiguration = is_readable($configFile) ? Yaml::parse(file_get_contents($configFile)) : [];

        return is_array($parsedConfiguration) ? $parsedConfiguration : [];
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
     */
    public function unregisterEventType($eventType)
    {
        $this->getEventType($eventType);

        unset($this->events[$eventType]);
    }

    /**
     * @param string $eventType
     * @return Event
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventType($eventType)
    {
        if (empty($eventType)) {
            throw new InvalidParameterException('Event type not defined');
        }

        if (empty($this->events[$eventType])) {
            throw new NotRegisteredException('Event type `' . $eventType . '` is not registered');
        }

        return $this->events[$eventType];
    }

    /**
     * @param Event $event
     * @throws AlreadyRegisteredException
     */
    public function registerEventType(Event $event)
    {
        $eventType = $event->getType();

        if (!empty($this->events[$eventType])) {
            throw new AlreadyRegisteredException('Event type `' . $eventType . '` already registered');
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

        return $event->getWatchers();
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