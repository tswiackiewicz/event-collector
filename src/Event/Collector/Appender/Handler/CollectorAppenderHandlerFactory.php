<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorSyslogAppender;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;

/**
 * Class CollectorAppenderHandlerFactory
 * @package TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler
 */
class CollectorAppenderHandlerFactory
{
    /**
     * @param CollectorAppender $appender
     * @return CollectorAppenderHandler
     * @throws UnknownTypeException
     */
    public function createFromCollectorAppender(CollectorAppender $appender)
    {
        $appenderType = $appender->getType();
        switch ($appenderType) {
            case CollectorAppender::SYSLOG_APPENDER:
                $logger = $this->createMonologSyslogLogger(
                    $appender->getParameter(CollectorSyslogAppender::IDENT_PARAMETER)
                );

                return new CollectorAppenderHandler($logger);
        }

        throw new UnknownTypeException('Unknown collector appender type: `' . $appenderType . '``');
    }

    /**
     * @param string $ident
     * @return LoggerInterface
     */
    private function createMonologSyslogLogger($ident)
    {
        $handler = new SyslogHandler($ident);
        $handler->setFormatter(
            new LineFormatter('[%datetime%] %channel% [%level_name%]: %message%')
        );

        $logger = new Logger($ident);
        $logger->pushHandler($handler);

        return $logger;
    }
}