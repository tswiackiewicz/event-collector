<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector
 */
class CollectorFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateCollector()
    {
        $factory = $this->createCollectorFactory();
        $collector = $factory->create(
            'test_event',
            '{"name":"test_collector","appender":{"type":"syslog","ident":"test"}}'
        );

        $this->assertInstanceOf(Collector::class, $collector);
    }

    /**
     * @return CollectorFactory
     */
    private function createCollectorFactory()
    {
        return new CollectorFactory();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenCollectorAppenderTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createCollectorFactory();
        $factory->create('test_event', '{"appender":[]}');
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenCollectorAppenderTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createCollectorFactory();
        $factory->create('test_event', '{"appender":{"type":"unknown_type"}}');
    }

    /**
     * @test
     */
    public function shouldCreateCollectorFromArray()
    {
        $factory = $this->createCollectorFactory();
        $collector = $factory->createFromArray(
            'test_event',
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'name' => 'test_collector',
                'appender' => [
                    'type' => 'syslog',
                    'ident' => 'test'
                ]
            ]
        );

        $this->assertInstanceOf(Collector::class, $collector);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenCreatedFromArrayCollectorAppenderTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createCollectorFactory();
        $factory->createFromArray('test_event', ['appender' => []]);
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenCreatedFromArrayCollectorAppenderTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createCollectorFactory();
        $factory->createFromArray(
            'test_event',
            [
                'appender' => [
                    'type' => 'unknown_type'
                ]
            ]
        );
    }
}
