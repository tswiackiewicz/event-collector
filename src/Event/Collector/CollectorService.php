<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Settings\SettingsRepository;

/**
 * Class CollectorService
 * @package TSwiackiewicz\EventsCollector\Event\Collector
 */
class CollectorService
{
    /**
     * @var SettingsRepository
     */
    private $repository;

    /**
     * @var CollectorAppenderHandlerFactory
     */
    private $factory;

    /**
     * @param SettingsRepository $repository
     * @param CollectorAppenderHandlerFactory $factory
     */
    public function __construct(SettingsRepository $repository, CollectorAppenderHandlerFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param string $eventType
     * @return Collector[]
     */
    public function getEventCollectors($eventType)
    {
        return $this->repository->getEventCollectors($eventType);
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     * @return Collector
     */
    public function getEventCollector($eventType, $collectorName)
    {
        return $this->repository->getEventCollector($eventType, $collectorName);
    }

    /**
     * @param Collector $collector
     */
    public function registerEventCollector(Collector $collector)
    {
        $this->repository->registerEventCollector($collector);
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     */
    public function unregisterEventCollector($eventType, $collectorName)
    {
        $this->repository->unregisterEventCollector($eventType, $collectorName);
    }

    /**
     * @param Event $event
     * @param string $eventPayload
     * @throws NotRegisteredException
     */
    public function collect(Event $event, $eventPayload)
    {
        $collectors = $event->getCollectors();
        if(empty($collectors)) {
            throw new NotRegisteredException('Collectors are not registered for event type `' . $event->getType() . '`');
        }

        foreach ($collectors as $collector) {
            $handler = $this->factory->createFromCollectorAppender($collector->getAppender());
            $handler->handle($event->getId(), $eventPayload);
        }
    }
}