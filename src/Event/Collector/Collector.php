<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorAppender;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class Collector
 * @package TSwiackiewicz\EventsCollector\Event\Collector
 */
class Collector
{
    const VALID_COLLECTOR_NAME_PATTERN = '[a-zA-Z][a-zA-Z0-9_-]+';

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
     * @var CollectorAppender
     */
    private $appender;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $event
     * @param CollectorAppender $appender
     */
    public function __construct(Uuid $id, $name, $event, CollectorAppender $appender)
    {
        $this->id = $id;
        $this->name = $name;
        $this->event = $event;
        $this->appender = $appender;

        $this->validateName();
        $this->validateEvent();
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateName()
    {
        if(empty($this->name) || !is_string($this->name)) {
            throw new InvalidParameterException('Not empty collector name is required');
        }
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateEvent()
    {
        if(empty($this->event) || !is_string($this->event)) {
            throw new InvalidParameterException('Not empty collector event type is required');
        }
    }

    /**
     * @param string $name
     * @param string $event
     * @param CollectorAppender $appender
     * @return Collector
     */
    public static function create($name, $event, CollectorAppender $appender)
    {
        return new static(Uuid::generate(), $name, $event, $appender);
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return CollectorAppender
     */
    public function getAppender()
    {
        return $this->appender;
    }

    /**
     * @return string
     */
    public function getAppenderType()
    {
        return $this->appender->getType();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_id' => $this->getId(),
            'name' => $this->getName(),
            'appender' => $this->appender->toArray()
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
}
