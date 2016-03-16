<?php
namespace TSwiackiewicz\EventsCollector\Settings;

use Symfony\Component\Yaml\Yaml;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

/**
 * Class InMemorySettings
 * @package TSwiackiewicz\EventsCollector\Settings
 */
class InMemorySettings implements Settings
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
     * @param string $file
     * @return InMemorySettings
     */
    public static function loadFromFile($file = '')
    {
        $settings = new static();
        $parsedConfiguration = $settings->getParsedSettings($file);

        $factory = new EventFactory();
        foreach ($parsedConfiguration as $eventConfiguration) {
            $event = $factory->createFromArray($eventConfiguration);
            $settings->events[$event->getType()] = $event;
        }

        return $settings;
    }

    /**
     * @param string $file
     * @return array
     */
    private function getParsedSettings($file = '')
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

        $parsedSettings = is_readable($configFile) ? Yaml::parse(file_get_contents($configFile)) : [];

        return is_array($parsedSettings) ? $parsedSettings : [];
    }

    /**
     * @param string $file
     */
    public function dump($file)
    {
        $dump = [];
        foreach ($this->events as $eventType => $event) {
            /** @var Event $event */
            $dump[] = $event->dump();
        }

        file_put_contents($file, Yaml::dump($dump));
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
     * @throws AlreadyRegisteredException
     */
    public function registerEvent(Event $event)
    {
        $eventType = $event->getType();

        if (!empty($this->events[$eventType])) {
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
        if (empty($eventType)) {
            throw new InvalidParameterException('Event type not defined');
        }

        if (empty($this->events[$eventType])) {
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