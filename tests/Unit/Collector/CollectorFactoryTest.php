<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Collector;

use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\CollectorFactory;
use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;
use TSwiackiewicz\EventsCollector\Collector\Exception\UnknownCollectorTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Collector
 */
class CollectorFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateAction()
    {
        $factory = $this->createCollectorFactory();

        $action = $factory->create(
            'test_event',
            '{"name":"test_collector","target":{"type":"syslog","ident":"test"}}'
        );

        $this->assertInstanceOf(Collector::class, $action);
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
    public function shouldThrowInvalidCollectorParameterExceptionIfCollectorTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidCollectorParameterException::class);

        $factory = $this->createCollectorFactory();

        $factory->create(
            'test_event',
            '{"target":[]}'
        );
    }

    /**
     * @test
     */
    public function shouldThrowUnknownCollectorTypeExceptionIfCollectorTypeIsUnknown()
    {
        $this->setExpectedException(UnknownCollectorTypeException::class);

        $factory = $this->createCollectorFactory();

        $factory->create(
            'test_event',
            '{"target":{"type":"unknown_type"}}'
        );
    }
}
