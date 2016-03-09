<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector\Appender;

use Symfony\Component\HttpFoundation\ParameterBag;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class CollectorSyslogAppender
 * @package TSwiackiewicz\EventsCollector\Event\Collector\Appender
 */
class CollectorSyslogAppender extends CollectorAppender
{
    const IDENT_PARAMETER = 'ident';

    /**
     * @param array $parameters
     * @return CollectorSyslogAppender
     */
    public static function create(array $parameters)
    {
        return new static(self::SYSLOG_APPENDER, new ParameterBag($parameters));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::SYSLOG_APPENDER,
            'ident' => $this->parameters->get(self::IDENT_PARAMETER)
        ];
    }

    /**
     * @throws InvalidParameterException
     */
    protected function validateParameters()
    {
        $ident = $this->parameters->get(self::IDENT_PARAMETER);

        if(empty($ident)) {
            throw new InvalidParameterException('Not empty syslog collector ident parameter is required');
        }
    }
}
