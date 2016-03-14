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
     */
    public function shouldCreateWatcher()
    {
        $factory = $this->createWatcherFactory();
        $watcher = $factory->create(
            'test_event',
            '{"name":"test_action","threshold":100,"aggregator":{"type":"fields","fields":["field1","field2"]},"action":{"type":"email","to":"user@domain.com","subject":"Test subject"}}'
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
        $factory->create('test_event', '{"aggregator":{"type":"fields","fields":["field1","field2"]},"action":[]}');
    }

    /**
     * @test
     */
    public function shouldThrowUnknownExceptionWhenGivenWatcherActionTypeIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createWatcherFactory();
        $factory->create('test_event', '{"aggregator":{"type":"fields","fields":["field1","field2"]},"action":{"type":"unknown_type"}}');
    }

    /**
     * @test
     */
    public function shouldCreateWatcherFromArray()
    {
        $factory = $this->createWatcherFactory();
        $watcher = $factory->createFromArray(
            'test_event',
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'name' => 'test_action',
                'threshold' => 100,
                'aggregator' => [
                    'type' => 'fields',
                    'fields' => [
                        'field1',
                        'field2'
                    ]
                ],
                'action' => [
                    'type' => 'email',
                    'to' => 'user@domain.com',
                    'subject' => 'Test subject'
                ]
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
                    'type' => 'fields',
                    'fields' => [
                        'field1',
                        'field2'
                    ]
                ],
                'action' => []
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
                    'type' => 'fields',
                    'fields' => [
                        'field1',
                        'field2'
                    ]
                ],
                'action' => [
                    'type' => 'unknown_type'
                ]
            ]
        );
    }
}
