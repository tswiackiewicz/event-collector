<?php
namespace TSwiackiewicz\EventsCollector\Collector\Syslog;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use TSwiackiewicz\EventsCollector\Collector\CollectorTarget;
use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;

/**
 * Class SyslogCollectorTarget
 * @package TSwiackiewicz\EventsCollector\Collector\Syslog
 */
class SyslogCollectorTarget extends CollectorTarget
{
    const SYSLOG_COLLECTOR = 'syslog';
    const IDENT_PARAMETER = 'ident';

    /**
     * @param array $parameters
     * @return SyslogCollectorTarget
     */
    public static function create(array $parameters)
    {
        return new static(self::SYSLOG_COLLECTOR, new ParameterBag($parameters));
    }

    /**
     * @throws InvalidCollectorParameterException
     */
    protected function validateParameters()
    {
        $ident = $this->parameters->get('ident');

        if (empty($ident)) {
            throw new InvalidCollectorParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Syslog ident parameter is required');
        }
    }
}
