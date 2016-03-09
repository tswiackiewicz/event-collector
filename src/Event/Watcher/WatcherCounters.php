<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class WatcherCounters
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class WatcherCounters
{
    /**
     * @var array
     */
    private $counters = [];

    /**
     * @param array $counters
     */
    public function __construct(array $counters)
    {
        $this->counters = $counters;
    }

    /**
     * @return WatcherCounters
     */
    public static function init()
    {
        return new static([]);
    }

    /**
     * @param string $key
     * @return int
     */
    public function increaseCounter($key)
    {
        $currentCounter = $this->getCounter($key);
        $this->counters[$key] = ++$currentCounter;

        return $currentCounter;
    }

    /**
     * @param string $key
     * @return int
     */
    public function getCounter($key)
    {
        $this->validateCounterKey($key);

        return isset($this->counters[$key]) ? $this->counters[$key] : 0;
    }

    /**
     * @param string $key
     * @throws InvalidParameterException
     */
    private function validateCounterKey($key)
    {
        if(empty($key) || !is_string($key)) {
            throw new InvalidParameterException('Not empty watcher counter key is required');
        }
    }

    /**
     * @return array
     */
    public function getCounters()
    {
        return $this->counters;
    }

    public function clear()
    {
        $this->counters = [];
    }
}