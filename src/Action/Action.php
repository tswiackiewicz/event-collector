<?php
namespace TSwiackiewicz\EventsCollector\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Action\Exception\InvalidActionParameterException;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class Action
 * @package TSwiackiewicz\EventsCollector\Action
 */
class Action
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
     * @var string[]
     */
    private $aggregationKey;

    /**
     * @var ActionTarget
     */
    private $target;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $event
     * @param int $threshold
     * @param string[] $aggregationKey
     * @param ActionTarget $target
     */
    public function __construct(Uuid $id, $name, $event, $threshold, array $aggregationKey, ActionTarget $target)
    {
        $this->id = $id;
        $this->name = $name;
        $this->event = $event;
        $this->threshold = $threshold;
        $this->aggregationKey = $aggregationKey;
        $this->target = $target;

        $this->validateName();
        $this->validateEvent();
        $this->validateThreshold();
    }

    /**
     * @throws InvalidActionParameterException
     */
    private function validateName()
    {
        if (empty($this->name) || !is_string($this->name)) {
            throw new InvalidActionParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Action name is required');
        }
    }

    /**
     * @throws InvalidActionParameterException
     */
    private function validateEvent()
    {
        if (empty($this->event) || !is_string($this->event)) {
            throw new InvalidActionParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Event type is required');
        }
    }

    /**
     * @throws InvalidActionParameterException
     */
    private function validateThreshold()
    {
        if (empty($this->threshold) || !is_int($this->threshold) || $this->threshold < 0) {
            throw new InvalidActionParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Threshold (greater than zero) value is required');
        }
    }

    /**
     * @param string $name
     * @param string $event
     * @param int $threshold
     * @param string[] $aggregationKey
     * @param ActionTarget $target
     * @return Action
     */
    public static function create($name, $event, $threshold, array $aggregationKey, ActionTarget $target)
    {
        return new static(
            Uuid::generate(),
            $name,
            $event,
            $threshold,
            $aggregationKey,
            $target
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
     * @return ActionTarget
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
            'threshold' => $this->getThreshold(),
            'aggregation_key' => $this->getAggregationKey(),
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

    /**
     * @return int
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * @return string[]
     */
    public function getAggregationKey()
    {
        return $this->aggregationKey;
    }
}