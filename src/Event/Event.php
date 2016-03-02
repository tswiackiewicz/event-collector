<?php
namespace TSwiackiewicz\EventsCollector\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Action\Action;
use TSwiackiewicz\EventsCollector\Action\Exception\ActionAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Action\Exception\NotRegisteredActionException;
use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\Exception\CollectorAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Collector\Exception\NotRegisteredCollectorException;
use TSwiackiewicz\EventsCollector\Event\Exception\InvalidEventParameterException;
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
     * @var Action[]
     */
    private $actions;

    /**
     * @param Uuid $id
     * @param string $type
     * @param Collector[] $collectors
     * @param Action[] $actions
     */
    public function __construct(Uuid $id, $type, array $collectors = [], array $actions = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->collectors = $collectors;
        $this->actions = $actions;

        $this->validateType();
    }

    /**
     * @throws InvalidEventParameterException
     */
    private function validateType()
    {
        if (empty($this->type) || !is_string($this->type)) {
            throw new InvalidEventParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Event type is required');
        }
    }

    /**
     * @param string $type
     * @param Collector[] $collectors
     * @param Action[] $actions
     * @return Event
     */
    public static function create($type, array $collectors = [], array $actions = [])
    {
        return new static(
            Uuid::generate(),
            $type,
            $collectors,
            $actions
        );
    }

    /**
     * @param Collector $collector
     * @throws CollectorAlreadyRegisteredException
     */
    public function addCollector(Collector $collector)
    {
        $collectorName = $collector->getName();

        if (!empty($this->collectors[$collectorName])) {
            throw new CollectorAlreadyRegisteredException(
                JsonResponse::HTTP_CONFLICT,
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
     * @throws NotRegisteredCollectorException
     */
    public function getCollector($collectorName)
    {
        if (empty($this->collectors[$collectorName])) {
            throw new NotRegisteredCollectorException(
                JsonResponse::HTTP_NOT_FOUND,
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
     * @param Action $action
     * @throws ActionAlreadyRegisteredException
     */
    public function addAction(Action $action)
    {
        $actionName = $action->getName();

        if (!empty($this->actions[$actionName])) {
            throw new ActionAlreadyRegisteredException(
                JsonResponse::HTTP_CONFLICT,
                'Action `' . $actionName . '` already registered for event `' . $this->type . '`'
            );
        }

        $this->actions[$actionName] = $action;
    }

    /**
     * @param string $actionName
     */
    public function removeAction($actionName)
    {
        $this->getAction($actionName);

        unset($this->actions[$actionName]);
    }

    /**
     * @param string $actionName
     * @return Action
     * @throws NotRegisteredActionException
     */
    public function getAction($actionName)
    {
        if (empty($this->actions[$actionName])) {
            throw new NotRegisteredActionException(
                JsonResponse::HTTP_NOT_FOUND,
                'Action `' . $actionName . '` is not registered for event type `' . $this->type . '`'
            );
        }

        return $this->actions[$actionName];
    }

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
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

        $actions = [];
        foreach ($this->actions as $action) {
            $actions[] = [
                '_id' => $action->getId(),
                'name' => $action->getName()
            ];
        }

        return [
            '_id' => $this->getId(),
            'type' => $this->getType(),
            'collectors' => $collectors,
            'actions' => $actions
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
}