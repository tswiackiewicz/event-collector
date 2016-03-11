<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Aggregator;

use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\FieldsBasedWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class FieldsBasedWatchedEventAggregatorTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Aggregator
 */
class FieldsBasedWatchedEventAggregatorTest extends BaseTestCase
{
    /**
     * @var string[]
     */
    private $fields = ['field1', 'field2'];

    /**
     * @test
     */
    public function shouldBuildFieldsBasedAggregationKey()
    {
        $eventType = 'test_event';
        $aggregator = $this->createAggregator();

        $this->assertEquals('test_event__field1__field2', $aggregator->buildAggregationKey($eventType));
    }

    /**
     * @return FieldsBasedWatchedEventAggregator
     */
    private function createAggregator()
    {
        return new FieldsBasedWatchedEventAggregator($this->fields);
    }

    /**
     * @test
     */
    public function shouldReturnFieldBasedAggregatorAsArray()
    {
        $aggregator = $this->createAggregator();

        $this->assertEquals(
            [
                'type',
                'fields'
            ],
            array_keys($aggregator->toArray())
        );
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenFieldsListIsEmpty()
    {
        $this->setExpectedException(InvalidParameterException::class);

        new FieldsBasedWatchedEventAggregator([]);
    }
}
