<?php
namespace TSwiackiewicz\EventsCollector\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class Collector
 * @package TSwiackiewicz\EventsCollector\Collector
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
     * @var CollectorTarget
     */
    private $target;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $event
     * @param CollectorTarget $target
     */
    public function __construct(Uuid $id, $name, $event, CollectorTarget $target)
    {
        $this->id = $id;
        $this->name = $name;
        $this->event = $event;
        $this->target = $target;

        $this->validateName();
        $this->validateEvent();
    }

    /**
     * @throws InvalidCollectorParameterException
     */
    private function validateName()
    {
        if (empty($this->name) || !is_string($this->name)) {
            throw new InvalidCollectorParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Collector name is required');
        }
    }

    /**
     * @throws InvalidCollectorParameterException
     */
    private function validateEvent()
    {
        if (empty($this->event) || !is_string($this->event)) {
            throw new InvalidCollectorParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Event type is required');
        }
    }

    /**
     * @param string $name
     * @param string $event
     * @param CollectorTarget $target
     * @return Collector
     */
    public static function create($name, $event, CollectorTarget $target)
    {
        return new static(
            Uuid::generate(),
            $name,
            $event,
            $target
        );
    }

    /**
     * @param array $collectorConfiguration
     * @return Collector
     */
    public static function createFromArray(array $collectorConfiguration)
    {
        // TODO
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return CollectorTarget
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_id' => $this->getId(),
            'name' => $this->getName(),
            'target' => [
                'type' => $this->target->getType(),
                'parameters' => $this->target->getParameters()
            ]
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
