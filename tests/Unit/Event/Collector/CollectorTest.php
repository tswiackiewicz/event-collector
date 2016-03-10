<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorSyslogAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Collector;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector
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
     * @var CollectorAppender
     */
    private $appender;

    /**
     * @var string
     */
    private $ident = 'test';

    /**
     * @test
     */
    public function shouldCreateCollector()
    {
        $this->createCollectorSyslogAppender();

        $collector = Collector::create(
            $this->name,
            $this->event,
            $this->appender
        );

        $this->assertCollector($collector);
    }

    private function createCollectorSyslogAppender()
    {
        $this->appender = CollectorSyslogAppender::create(
            [
                CollectorSyslogAppender::IDENT_PARAMETER => $this->ident
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
        $this->assertEquals($this->appender, $collector->getAppender());
        $this->assertEquals('syslog', $collector->getAppenderType());
    }

    /**
     * @test
     */
    public function shouldReturnCollectorAsArray()
    {
        $this->createCollectorSyslogAppender();

        $collector = Collector::create(
            $this->name,
            $this->event,
            $this->appender
        );

        $this->assertEquals(
            [
                '_id',
                'name',
                'appender'
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
    public function shouldThrowInvalidParameterExceptionWhenCollectorIsInvalid(array $invalidParameters)
    {
        $this->setExpectedException(InvalidParameterException::class);

        Collector::create(
            $invalidParameters['name'],
            $invalidParameters['event'],
            $invalidParameters['appender']
        );
    }

    /**
     * @return array
     */
    public function getInvalidCollectorParameters()
    {
        $this->createCollectorSyslogAppender();

        return [
            [
                [
                    'name' => '',
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => false,
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => true,
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => null,
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => [],
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => 0,
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => 1234,
                    'event' => $this->event,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => false,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => true,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => [],
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 0,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 1234,
                    'appender' => $this->appender
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'appender' => $this->appender
                ]
            ]
        ];
    }
}
