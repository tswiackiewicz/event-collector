<?php
namespace TSwiackiewicz\EventsCollector\Settings\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;

/**
 * Interface CollectorSettings
 * @package TSwiackiewicz\EventsCollector\Settings\Event\Collector
 */
interface CollectorSettings
{
    /**
     * @param string $eventType
     * @return Collector[]
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventCollectors($eventType);

    /**
     * @param string $eventType
     * @param string $collectorName
     * @return Collector
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function getEventCollector($eventType, $collectorName);

    /**
     * @param Collector $collector
     * @throws AlreadyRegisteredException
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function registerEventCollector(Collector $collector);

    /**
     * @param string $eventType
     * @param string $collectorName
     * @throws InvalidParameterException
     * @throws NotRegisteredException
     */
    public function unregisterEventCollector($eventType, $collectorName);
}