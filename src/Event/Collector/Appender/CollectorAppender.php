<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector\Appender;

use Symfony\Component\HttpFoundation\ParameterBag;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class CollectorAppender
 * @package TSwiackiewicz\EventsCollector\Event\Collector\Appender
 */
abstract class CollectorAppender
{
    const SYSLOG_APPENDER = 'syslog';

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
     * @throws InvalidParameterException
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

    /**
     * @return array
     */
    abstract public function toArray();
}
