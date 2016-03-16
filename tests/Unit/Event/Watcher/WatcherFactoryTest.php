<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherFactory;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher
 */
class WatcherFactoryTest extends BaseTestCase
{
    /**
     * @test
     * @dataProvider getWatcherConfigurationParameters
     *
     * @param string $aggregatorConfiguration
     * @param string $actionConfiguration
     */
    public function shouldCreateWatcher($aggregatorConfiguration, $actionConfiguration)
    {
        $factory = $this->createWatcherFactory();
        $watcher = $factory->create(
            'test_event',
            '{"name":"test_action","threshold":100,"aggregator":' . $aggregatorConfiguration . ',"action":' . $actionConfiguration . '}'
        );

        $this->assertInstanceOf(Watcher::class, $watcher);
    }

    /**
     * @return WatcherFactory
     */
    private function createWatcherFactory()
    {
        return new WatcherFactory();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenWatcherActionTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createWatcherFactory();
        $factory->create('test_event', '{"aggregator":{"type":"single"},"action":[]}');
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenWatcherAggregatorTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createWatcherFactory();
        $factory->create('test_event', '{"aggregator":[],"action":{"type": "null"}}');
    }

    /**
     * @test
     */
    public function shouldThrowUnknownExceptionWhenGivenWatcherActionTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createWatcherFactory();
        $factory->create('test_event',
            '{"aggregator":{"type":"single"},"action":{"type":"unknown_type"}}');
    }

    /**
     * @test
     * @dataProvider getWatcherConfigurationParameters
     *
     * @param string $aggregatorConfiguration
     * @param string $actionConfiguration
     */
    public function shouldCreateWatcherFromArray($aggregatorConfiguration, $actionConfiguration)
    {
        $factory = $this->createWatcherFactory();
        $watcher = $factory->createFromArray(
            'test_event',
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'name' => 'test_action',
                'threshold' => 100,
                'aggregator' => json_decode($aggregatorConfiguration, true),
                'action' => json_decode($actionConfiguration, true)
            ]
        );

        $this->assertInstanceOf(Watcher::class, $watcher);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenCreatedFromArrayWatcherActionTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createWatcherFactory();
        $factory->createFromArray(
            'test_event',
            [
                'aggregator' => [
                    'type' => 'single'
                ],
                'action' => []
            ]
        );
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenCreatedFromArrayWatcherAggregatorTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $factory = $this->createWatcherFactory();
        $factory->createFromArray(
            'test_event',
            [
                'aggregator' => [],
                'action' => [
                    'type' => 'null'
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenCreatedFromArrayWatcherActionTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createWatcherFactory();
        $factory->createFromArray(
            'test_event',
            [
                'aggregator' => [
                    'type' => 'single'
                ],
                'action' => [
                    'type' => 'unknown_type'
                ]
            ]
        );
    }

    /**
     * @return array
     */
    public function getWatcherConfigurationParameters()
    {
        return [
            [
                '{"type":"fields","fields":["field1","field2"]}',
                '{"type":"email","to":"user@domain.com","subject":"Test subject"}'
            ],
            [
                '{"type":"single"}',
                '{"type":"email","to":"user@domain.com","subject":"Test subject"}'
            ],
            [
                '{"type":"fields","fields":["field1","field2"]}',
                '{"type":"null"}'
            ],
            [
                '{"type":"single"}',
                '{"type":"null"}'
            ]
        ];
    }
}
