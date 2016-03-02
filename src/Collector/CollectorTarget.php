<?php
namespace TSwiackiewicz\EventsCollector\Collector;

use Symfony\Component\HttpFoundation\ParameterBag;
use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;

/**
 * Class CollectorTarget
 * @package TSwiackiewicz\EventsCollector\Collector
 */
abstract class CollectorTarget
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var ParameterBag
     */
    protected $parameters;

    /**
     * @param string $type
     * @param ParameterBag $parameters
     */
    public function __construct($type, ParameterBag $parameters)
    {
        $this->type = $type;
        $this->parameters = $parameters;

        $this->validateParameters();
    }

    /**
     * @throws InvalidCollectorParameterException
     */
    abstract protected function validateParameters();

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameters->get($key);
    }
}
