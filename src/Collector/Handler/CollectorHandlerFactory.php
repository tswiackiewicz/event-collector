<?php
namespace TSwiackiewicz\EventsCollector\Collector\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\Syslog\SyslogCollectorTarget;

/**
 * Class CollectorHandlerFactory
 * @package TSwiackiewicz\EventsCollector\Collector\Handler
 */
class CollectorHandlerFactory
{
    /**
     * @param Collector $collector
     * @return CollectorHandler
     */
    public function createFromCollector(Collector $collector)
    {
        $type = $collector->getTargetType();
        switch ($type) {
            case SyslogCollectorTarget::SYSLOG_COLLECTOR:
                $target = $collector->getTarget();
                $logger = $this->createMonologSyslogLogger(
                    $collector->getName(),
                    $collector->getEvent(),
                    $target->getParameter(SyslogCollectorTarget::IDENT_PARAMETER)
                );

                return new CollectorHandler($logger);
        }
    }

    /**
     * @param string $collectorName
     * @param string $eventType
     * @param string $ident
     * @return LoggerInterface
     */
    private function createMonologSyslogLogger($collectorName, $eventType, $ident)
    {
        $loggerName = $eventType . '_' . $collectorName;

        $handler = new SyslogHandler($ident);
        $handler->setFormatter(
            new LineFormatter('[%datetime%] %channel% [%level_name%]: %message%')
        );

        $logger = new Logger($loggerName);
        $logger->pushHandler($handler);

        return $logger;
    }
}