<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorSyslogAppender;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorSyslogAppenderTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender
 */
class CollectorSyslogAppenderTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $ident = 'test';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @test
     */
    public function shouldCreateCollectorSyslogAppender()
    {
        $this->parameters = [
            CollectorSyslogAppender::IDENT_PARAMETER => $this->ident
        ];

        $appender = CollectorSyslogAppender::create($this->parameters);

        $this->assertCollectorAppender($appender);
    }

    /**
     * @param CollectorSyslogAppender $appender
     */
    private function assertCollectorAppender(CollectorSyslogAppender $appender)
    {
        $this->assertEquals(CollectorAppender::SYSLOG_APPENDER, $appender->getType());
        $this->assertEquals($this->parameters, $appender->getParameters());
        $this->assertEquals($this->ident, $appender->getParameter(CollectorSyslogAppender::IDENT_PARAMETER));
        $this->assertEquals(
            [
                'type' => CollectorAppender::SYSLOG_APPENDER,
                'ident' => $this->ident
            ],
            $appender->toArray()
        );
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionIfSyslogIdentIsInvalid()
    {
        $this->setExpectedException(InvalidParameterException::class);

        CollectorSyslogAppender::create([]);
    }
}
