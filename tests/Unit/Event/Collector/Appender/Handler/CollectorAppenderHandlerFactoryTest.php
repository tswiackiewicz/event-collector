<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender\Handler;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorSyslogAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandler;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandlerFactory;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorAppenderHandlerFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender\Handler
 */
class CollectorAppenderHandlerFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateCollectorAppenderHandlerFromGivenCollectorAppender()
    {
        $appender = CollectorSyslogAppender::create(['ident' => 'test']);

        $factory = new CollectorAppenderHandlerFactory();
        $handler = $factory->createFromCollectorAppender($appender);

        $this->assertInstanceOf(CollectorAppenderHandler::class, $handler);
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenGivenCollectorAppenderTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);
        
        $appender = $this->createCollectorAppenderWithUnknownType();

        $factory = new CollectorAppenderHandlerFactory();
        $factory->createFromCollectorAppender($appender);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CollectorAppender
     */
    private function createCollectorAppenderWithUnknownType()
    {
        $appender = $this->getMockBuilder(CollectorAppender::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getType'
                ]
            )
            ->getMockForAbstractClass();
        $appender->expects($this->once())->method('getType')->willReturn('unknown_type');

        return $appender;
    }
}
