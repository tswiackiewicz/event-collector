<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Settings\Settings;

/**
 * Class CollectorService
 * @package TSwiackiewicz\EventsCollector\Event\Collector
 */
class CollectorService
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var CollectorAppenderHandlerFactory
     */
    private $factory;

    /**
     * @param Settings $settings
     * @param CollectorAppenderHandlerFactory $factory
     */
    public function __construct(Settings $settings, CollectorAppenderHandlerFactory $factory)
    {
        $this->settings = $settings;
        $this->factory = $factory;
    }

    /**
     * @param Settings $settings
     * @return CollectorService
     */
    public static function create(Settings $settings)
    {
        return new static(
            $settings,
            new CollectorAppenderHandlerFactory()
        );
    }

    /**
     * @param string $eventType
     * @return Collector[]
     */
    public function getEventCollectors($eventType)
    {
        return $this->settings->getEventCollectors($eventType);
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     * @return Collector
     */
    public function getEventCollector($eventType, $collectorName)
    {
        return $this->settings->getEventCollector($eventType, $collectorName);
    }

    /**
     * @param Collector $collector
     */
    public function registerEventCollector(Collector $collector)
    {
        $this->settings->registerEventCollector($collector);
    }

    /**
     * @param string $eventType
     * @param string $collectorName
     */
    public function unregisterEventCollector($eventType, $collectorName)
    {
        $this->settings->unregisterEventCollector($eventType, $collectorName);
    }

    /**
     * @param Event $event
     * @param string $eventPayload
     * @throws NotRegisteredException
     */
    public function collect(Event $event, $eventPayload)
    {
        $collectors = $event->getCollectors();
        if (empty($collectors)) {
            throw new NotRegisteredException('Collectors are not registered for event type `' . $event->getType() . '`');
        }

        foreach ($collectors as $collector) {
            $handler = $this->factory->createFromCollectorAppender($collector->getAppender());
            $handler->handle($event->getId(), $eventPayload);
        }
    }
}