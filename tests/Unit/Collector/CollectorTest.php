<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Collector;

use TSwiackiewicz\EventsCollector\Collector\Collector;
use TSwiackiewicz\EventsCollector\Collector\CollectorTarget;
use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;
use TSwiackiewicz\EventsCollector\Collector\Syslog\SyslogCollectorTarget;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Collector
 */
class CollectorTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $name = 'test_collector';

    /**
     * @var string
     */
    private $event = 'test_event';

    /**
     * @var CollectorTarget
     */
    private $target;

    /**
     * @var string
     */
    private $ident = 'test';

    /**
     * @test
     */
    public function shouldCreateValidCollector()
    {
        $this->createSyslogCollectorTarget();

        $collector = Collector::create(
            $this->name,
            $this->event,
            $this->target
        );

        $this->assertCollector($collector);
    }

    private function createSyslogCollectorTarget()
    {
        $this->target = SyslogCollectorTarget::create(
            [
                SyslogCollectorTarget::IDENT_PARAMETER => $this->ident
            ]
        );
    }

    /**
     * @param Collector $collector
     */
    private function assertCollector(Collector $collector)
    {
        $this->assertEquals($this->name, $collector->getName());
        $this->assertEquals($this->event, $collector->getEvent());
        $this->assertEquals($this->target, $collector->getTarget());
    }

    /**
     * @test
     */
    public function shouldReturnActionAsArray()
    {
        $this->createSyslogCollectorTarget();

        $collector = Collector::create(
            $this->name,
            $this->event,
            $this->target
        );

        $this->assertEquals(
            [
                '_id',
                'name',
                'target'
            ],
            array_keys($collector->toArray())
        );
    }

    /**
     * @test
     * @dataProvider getInvalidCollectorParameters
     *
     * @param array $invalidParameters
     */
    public function shouldThrowInvalidCollectorParameterExceptionIfCollectorIsInvalid(array $invalidParameters)
    {
        $this->setExpectedException(InvalidCollectorParameterException::class);

        Collector::create(
            $invalidParameters['name'],
            $invalidParameters['event'],
            $invalidParameters['target']
        );
    }

    /**
     * @return array
     */
    public function getInvalidCollectorParameters()
    {
        $this->createSyslogCollectorTarget();

        return [
            [
                [
                    'name' => '',
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => false,
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => true,
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => null,
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => [],
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => 0,
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => 1234,
                    'event' => $this->event,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => false,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => true,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => [],
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 0,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 1234,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'target' => $this->target
                ]
            ]
        ];
    }
}
