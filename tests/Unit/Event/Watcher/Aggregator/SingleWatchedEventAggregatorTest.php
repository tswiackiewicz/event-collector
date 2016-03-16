<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Aggregator;

use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\SingleWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class SingleWatchedEventAggregatorTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Aggregator
 */
class SingleWatchedEventAggregatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldBuildSingleAggregatorAggregationKey()
    {
        $eventType = 'test_event';
        $aggregator = $this->createAggregator();

        $this->assertEquals($eventType, $aggregator->buildAggregationKey($eventType));
    }

    /**
     * @return SingleWatchedEventAggregator
     */
    private function createAggregator()
    {
        return new SingleWatchedEventAggregator();
    }

    /**
     * @test
     */
    public function shouldReturnSingleAggregatorAsArray()
    {
        $aggregator = $this->createAggregator();

        $this->assertEquals(
            [
                'type' => SingleWatchedEventAggregator::SINGLE_AGGREGATOR
            ],
            $aggregator->toArray()
        );
    }
}
