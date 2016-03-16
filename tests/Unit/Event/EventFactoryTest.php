<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\EventFactory;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class EventTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event
 */
class EventFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateEvent()
    {
        $factory = $this->createEventFactory();
        $event = $factory->createFromArray(
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'type' => 'test_event',
                'collectors' => [
                    [
                        '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                        'name' => 'test_collector',
                        'appender' => [
                            'type' => 'syslog',
                            'ident' => 'test'
                        ]
                    ]
                ],
                'watchers' => [
                    [
                        '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                        'name' => 'test_action',
                        'threshold' => 100,
                        'aggregator' => [
                            'type' => 'single',
                            'fields' => [
                                'field_name'
                            ]
                        ],
                        'action' => [
                            'type' => 'email',
                            'to' => 'user@domain.com',
                            'subject' => 'Test subject'
                        ]
                    ]
                ]
            ]
        );

        $this->assertInstanceOf(Event::class, $event);
    }

    /**
     * @return EventFactory
     */
    private function createEventFactory()
    {
        return new EventFactory();
    }
}
