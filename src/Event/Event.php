<?php
namespace TSwiackiewicz\EventsCollector\Event;

use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class EventType
 * @package TSwiackiewicz\EventsCollector\Event
 */
class Event
{
    const VALID_EVENT_TYPE_PATTERN = '[a-zA-Z][a-zA-Z0-9_-]+';

    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Collector[]
     */
    private $collectors;

    /**
     * @var Watcher[]
     */
    private $watchers;

    /**
     * @param Uuid $id
     * @param string $type
     * @param Collector[] $collectors
     * @param Watcher[] $watchers
     */
    public function __construct(Uuid $id, $type, array $collectors = [], array $watchers = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->collectors = $collectors;
        $this->watchers = $watchers;

        $this->validateType();
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateType()
    {
        if(empty($this->type) || !is_string($this->type)) {
            throw new InvalidParameterException('Event type is required');
        }
    }

    /**
     * @param string $type
     * @param Collector[] $collectors
     * @param Watcher[] $watchers
     * @return Event
     */
    public static function create($type, array $collectors = [], array $watchers = [])
    {
        return new static(
            Uuid::generate(),
            $type,
            $collectors,
            $watchers
        );
    }

    /**
     * @param Collector $collector
     * @throws AlreadyRegisteredException
     */
    public function addCollector(Collector $collector)
    {
        $collectorName = $collector->getName();

        if(!empty($this->collectors[$collectorName])) {
            throw new AlreadyRegisteredException(
                'Collector `' . $collectorName . '` already registered for event `' . $this->type . '`'
            );
        }

        $this->collectors[$collectorName] = $collector;
    }

    /**
     * @param string $collectorName
     */
    public function removeCollector($collectorName)
    {
        $this->getCollector($collectorName);

        unset($this->collectors[$collectorName]);
    }

    /**
     * @param string $collectorName
     * @return Collector
     * @throws NotRegisteredException
     */
    public function getCollector($collectorName)
    {
        if(empty($this->collectors[$collectorName])) {
            throw new NotRegisteredException(
                'Collector `' . $collectorName . '` is not registered for event type `' . $this->type . '`'
            );
        }

        return $this->collectors[$collectorName];
    }

    /**
     * @return Collector[]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * @param Watcher $watcher
     * @throws AlreadyRegisteredException
     */
    public function addWatcher(Watcher $watcher)
    {
        $watcherName = $watcher->getName();

        if(!empty($this->watchers[$watcherName])) {
            throw new AlreadyRegisteredException(
                'Watcher `' . $watcherName . '` already registered for event `' . $this->type . '`'
            );
        }

        $this->watchers[$watcherName] = $watcher;
    }

    /**
     * @param string $watcherName
     */
    public function removeWatcher($watcherName)
    {
        $this->getWatcher($watcherName);

        unset($this->watchers[$watcherName]);
    }

    /**
     * @param $watcherName
     * @return Watcher
     * @throws NotRegisteredException
     */
    public function getWatcher($watcherName)
    {
        if(empty($this->watchers[$watcherName])) {
            throw new NotRegisteredException(
                'Watcher `' . $watcherName . '` is not registered for event type `' . $this->type . '`'
            );
        }

        return $this->watchers[$watcherName];
    }

    /**
     * @return Watcher[]
     */
    public function getWatchers()
    {
        return $this->watchers;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $collectors = [];
        foreach ($this->collectors as $collector) {
            $collectors[] = [
                '_id' => $collector->getId(),
                'name' => $collector->getName()
            ];
        }

        $watchers = [];
        foreach ($this->watchers as $watcher) {
            $watchers[] = [
                '_id' => $watcher->getId(),
                'name' => $watcher->getName()
            ];
        }

        return [
            '_id' => $this->getId(),
            'type' => $this->getType(),
            'collectors' => $collectors,
            'watchers' => $watchers
        ];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id->getUuid();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function dump()
    {
        $collectors = [];
        foreach ($this->collectors as $collector) {
            $collectors[] = $collector->toArray();
        }

        $watchers = [];
        foreach ($this->watchers as $watcher) {
            $watchers[] = $watcher->toArray();
        }

        return [
            '_id' => $this->getId(),
            'type' => $this->getType(),
            'collectors' => $collectors,
            'watchers' => $watchers
        ];
    }
}