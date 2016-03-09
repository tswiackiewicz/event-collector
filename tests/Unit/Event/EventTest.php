<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event;

use TSwiackiewicz\EventsCollector\Action\Action;
use TSwiackiewicz\EventsCollector\Action\Email\EmailActionTarget;
use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\Syslog\SyslogCollectorTarget;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Exception\InvalidEventParameterException;
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
     * @var Action[]
     */
    private $actions = [];

    /**
     * @test
     */
    public function shouldCreateValidEvent()
    {
        $this->createEventCollectors();
        $this->createEventActions();

        $event = Event::create(
            $this->type,
            $this->collectors,
            $this->actions
        );

        $this->assertEvent($event);
    }

    private function createEventCollectors()
    {
        $this->collectors[] = Collector::create(
            'test_collector',
            $this->type,
            SyslogCollectorTarget::create(
                [
                    SyslogCollectorTarget::IDENT_PARAMETER => 'test'
                ]
            )
        );
    }

    private function createEventActions()
    {
        $this->actions[] = Action::create(
            'test_action',
            $this->type,
            100,
            [
                'field1'
            ],
            EmailActionTarget::create(
                [
                    EmailActionTarget::TO_ADDRESS_PARAMETER => 'test@domain.com',
                    EmailActionTarget::SUBJECT_PARAMETER => 'Test subject'
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
        $this->assertEquals($this->actions, $event->getWatchers());
    }

    /**
     * @test
     */
    public function shouldReturnEventAsArray()
    {
        $this->createEventCollectors();
        $this->createEventActions();

        $event = Event::create(
            $this->type,
            $this->collectors,
            $this->actions
        );

        $this->assertEquals(
            [
                '_id',
                'type',
                'collectors',
                'actions'
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
    public function shouldThrowInvalidEventParameterExceptionIfTypeIsInvalid($invalidType)
    {
        $this->setExpectedException(InvalidEventParameterException::class);

        Event::create(
            $invalidType
        );
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
