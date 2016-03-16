<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Aggregator;

use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\FieldsBasedWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\SingleWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\WatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\WatchedEventAggregatorFactory;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatchedEventAggregatorFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Aggregator
 */
class WatchedEventAggregatorFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateSingleWatchedEventAggregator()
    {
        $factory = $this->createWatchedEventAggregatorFactory();
        $aggregator = $factory->create(['type' => WatchedEventAggregator::SINGLE_AGGREGATOR]);

        $this->assertInstanceOf(SingleWatchedEventAggregator::class, $aggregator);
    }

    /**
     * @return WatchedEventAggregatorFactory
     */
    private function createWatchedEventAggregatorFactory()
    {
        return new WatchedEventAggregatorFactory();
    }

    /**
     * @test
     */
    public function shouldCreateFieldsBasedWatchedEventAggregator()
    {
        $factory = $this->createWatchedEventAggregatorFactory();
        $aggregator = $factory->create([
            'type' => WatchedEventAggregator::FIELDS_AGGREGATOR,
            'fields' => ['field_name']
        ]);

        $this->assertInstanceOf(FieldsBasedWatchedEventAggregator::class, $aggregator);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenAggregatorTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createWatchedEventAggregatorFactory();
        $factory->create([]);
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenAggregatorTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createWatchedEventAggregatorFactory();
        $factory->create(['type' => 'unknown_aggregator_type']);
    }
}
