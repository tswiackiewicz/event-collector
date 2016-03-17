<?php
namespace TSwiackiewicz\EventsCollector\Counters;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class InMemoryCounters
 * @package TSwiackiewicz\EventsCollector\Counters
 */
class InMemoryCounters implements Counters
{
    /**
     * @var array
     */
    private $counters = [];

    /**
     * @param array $counters
     */
    public function __construct(array $counters = [])
    {
        $this->counters = $counters;
    }

    /**
     * @param string $key
     * @return int
     * @throws InvalidParameterException
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
     * @throws InvalidParameterException
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
        if (empty($key) || !is_string($key)) {
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

    /**
     * @param string $key
     * @throws InvalidParameterException
     */
    public function resetCounter($key)
    {
        $this->initCounter($key, 0);
    }

    /**
     * @param string $key
     * @param int $value
     * @throws InvalidParameterException
     */
    public function initCounter($key, $value)
    {
        $this->validateCounterKey($key);

        $this->counters[$key] = $value;
    }

    public function clear()
    {
        $this->counters = [];
    }
}