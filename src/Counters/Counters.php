<?php
namespace TSwiackiewicz\EventsCollector\Counters;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Interface Counters
 * @package TSwiackiewicz\EventsCollector\Counters
 */
interface Counters
{
    /**
     * @param string $key
     * @return int
     * @throws InvalidParameterException
     */
    public function increaseCounter($key);

    /**
     * @param string $key
     * @return int
     * @throws InvalidParameterException
     */
    public function getCounter($key);

    /**
     * @return array
     */
    public function getCounters();

    public function clear();
}