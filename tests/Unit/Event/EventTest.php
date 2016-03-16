<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorSyslogAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherEmailAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\SingleWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class EventTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event
 */
class EventTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $type = 'test_event';

    /**
     * @var Collector[]
     */
    private $collectors = [];

    /**
     * @var Watcher[]
     */
    private $watchers = [];

    /**
     * @test
     */
    public function shouldCreateEvent()
    {
        $this->createEventCollectors();
        $this->createEventWatchers();

        $event = Event::create(
            $this->type,
            $this->collectors,
            $this->watchers
        );

        $this->assertEvent($event);
    }

    private function createEventCollectors()
    {
        $this->collectors[] = Collector::create(
            'test_collector',
            $this->type,
            CollectorSyslogAppender::create(
                [
                    CollectorSyslogAppender::IDENT_PARAMETER => 'test'
                ]
            )
        );
    }

    private function createEventWatchers()
    {
        $this->watchers[] = Watcher::create(
            'test_action',
            $this->type,
            100,
            new SingleWatchedEventAggregator(),
            WatcherEmailAction::create(
                [
                    WatcherEmailAction::TO_ADDRESS_PARAMETER => 'test@domain.com',
                    WatcherEmailAction::SUBJECT_PARAMETER => 'Test subject'
                ]
            )
        );
    }

    /**
     * @param Event $event
     */
    private function assertEvent(Event $event)
    {
        $this->assertEquals($this->type, $event->getType());
        $this->assertEquals($this->collectors, $event->getCollectors());
        $this->assertEquals($this->watchers, $event->getWatchers());
    }

    /**
     * @test
     */
    public function shouldReturnEventAsArray()
    {
        $this->createEventCollectors();
        $this->createEventWatchers();

        $event = Event::create(
            $this->type,
            $this->collectors,
            $this->watchers
        );

        $this->assertEquals(
            [
                '_id',
                'type',
                'collectors',
                'watchers'
            ],
            array_keys($event->toArray())
        );
    }

    /**
     * @test
     * @dataProvider getInvalidType
     *
     * @param string $invalidType
     */
    public function shouldThrowInvalidParameterExceptionWhenEventTypeIsInvalid($invalidType)
    {
        $this->setExpectedException(InvalidParameterException::class);

        Event::create(
            $invalidType
        );
    }

    /**
     * @test
     */
    public function shouldDumpEventWithDetailedCollectorsAndWatchersListsToArray()
    {
        $this->createEventCollectors();
        $this->createEventWatchers();

        $event = Event::create(
            $this->type,
            $this->collectors,
            $this->watchers
        );
        $dumpedEvent = $event->dump();

        $this->assertEquals(
            [
                '_id',
                'type',
                'collectors',
                'watchers'
            ],
            array_keys($dumpedEvent)
        );

        foreach ($dumpedEvent['collectors'] as $collector) {
            $this->assertEquals(
                [
                    '_id',
                    'name',
                    'appender'
                ],
                array_keys($collector)
            );
        }
        foreach ($dumpedEvent['watchers'] as $watcher) {
            $this->assertEquals(
                [
                    '_id',
                    'name',
                    'threshold',
                    'aggregator',
                    'action'
                ],
                array_keys($watcher)
            );
        }
    }

    /**
     * @return array
     */
    public function getInvalidType()
    {
        return [
            [
                false
            ],
            [
                true
            ],
            [
                null
            ],
            [
                []
            ],
            [
                ''
            ]
        ];
    }
}
