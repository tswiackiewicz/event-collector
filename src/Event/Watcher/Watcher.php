<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherAction as Action;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\WatchedEventAggregator as Aggregator;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class Watcher
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class Watcher
{
    const VALID_ACTION_NAME_PATTERN = '[a-zA-Z][a-zA-Z0-9_-]+';

    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $event;

    /**
     * @var int
     */
    private $threshold;

    /**
     * @var Aggregator
     */
    private $aggregator;

    /**
     * @var Action
     */
    private $action;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $event
     * @param int $threshold
     * @param Aggregator $aggregator
     * @param Action $action
     */
    public function __construct(Uuid $id, $name, $event, $threshold, Aggregator $aggregator, Action $action)
    {
        $this->id = $id;
        $this->name = $name;
        $this->event = $event;
        $this->threshold = $threshold;
        $this->aggregator = $aggregator;
        $this->action = $action;

        $this->validateName();
        $this->validateEvent();
        $this->validateThreshold();
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateName()
    {
        if(empty($this->name) || !is_string($this->name)) {
            throw new InvalidParameterException('Action name is required');
        }
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateEvent()
    {
        if(empty($this->event) || !is_string($this->event)) {
            throw new InvalidParameterException('Event type is required');
        }
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateThreshold()
    {
        if(empty($this->threshold) || !is_int($this->threshold) || $this->threshold < 0) {
            throw new InvalidParameterException('Threshold (greater than zero) value is required');
        }
    }

    /**
     * @param string $name
     * @param string $event
     * @param int $threshold
     * @param Aggregator $aggregator
     * @param Action $action
     * @return Watcher
     */
    public static function create($name, $event, $threshold, Aggregator $aggregator, Action $action)
    {
        return new static(
            Uuid::generate(),
            $name,
            $event,
            $threshold,
            $aggregator,
            $action
        );
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Aggregator
     */
    public function getAggregator()
    {
        return $this->aggregator;
    }

    /**
     * @return string
     */
    public function buildAggregationKey()
    {
        return $this->aggregator->buildAggregationKey($this->event);
    }

    /**
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_id' => $this->getId(),
            'name' => $this->getName(),
            'threshold' => $this->getThreshold(),
            'aggregator' => $this->aggregator->toArray(),
            'action' => $this->action->toArray()
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getThreshold()
    {
        return $this->threshold;
    }
}