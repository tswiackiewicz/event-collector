<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherEmailAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\FieldsBasedWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\SingleWatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator\WatchedEventAggregator;
use TSwiackiewicz\EventsCollector\Event\Watcher\Watcher;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher
 */
class WatcherTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $name = 'test_action';

    /**
     * @var string
     */
    private $event = 'test_event';

    /**
     * @var int
     */
    private $threshold = 100;

    /**
     * @var WatchedEventAggregator
     */
    private $aggregator;

    /**
     * @var WatcherAction
     */
    private $action;

    /**
     * @var string
     */
    private $toAddress = 'user@domain.com';

    /**
     * @var string
     */
    private $subject = 'Test subject';

    /**
     * @test
     */
    public function shouldCreateWatcher()
    {
        $this->createWatcherEmailAction();
        $this->createAggregator();

        $watcher = Watcher::create(
            $this->name,
            $this->event,
            $this->threshold,
            $this->aggregator,
            $this->action
        );

        $this->assertWatcher($watcher);
    }

    private function createWatcherEmailAction()
    {
        $this->action = WatcherEmailAction::create(
            [
                WatcherEmailAction::TO_ADDRESS_PARAMETER => $this->toAddress,
                WatcherEmailAction::SUBJECT_PARAMETER => $this->subject
            ]
        );
    }

    private function createAggregator()
    {
        $this->aggregator = new SingleWatchedEventAggregator();
    }

    /**
     * @param Watcher $watcher
     */
    private function assertWatcher(Watcher $watcher)
    {
        $this->assertEquals($this->name, $watcher->getName());
        $this->assertEquals($this->event, $watcher->getEvent());
        $this->assertEquals($this->threshold, $watcher->getThreshold());
        $this->assertEquals($this->aggregator, $watcher->getAggregator());
        $this->assertEquals($this->action, $watcher->getAction());
    }

    /**
     * @test
     */
    public function shouldBuildSingleAggregatorAggregationKey()
    {
        $aggregationKey = $this->event;

        $this->createWatcherEmailAction();
        $this->createAggregator();

        $watcher = Watcher::create(
            $this->name,
            $this->event,
            $this->threshold,
            $this->aggregator,
            $this->action
        );

        $this->assertEquals($aggregationKey, $watcher->buildAggregationKey());
    }

    /**
     * @test
     */
    public function shouldBuildFieldsBasedAggregatorAggregationKey()
    {
        $aggregationKey = implode(
            FieldsBasedWatchedEventAggregator::KEY_PARTS_SEPARATOR,
            [
                $this->event,
                'field1',
                'field2'
            ]
        );

        $this->createWatcherEmailAction();
        $this->createAggregator();

        $watcher = Watcher::create(
            $this->name,
            $this->event,
            $this->threshold,
            new FieldsBasedWatchedEventAggregator(['field1', 'field2']),
            $this->action
        );

        $this->assertEquals($aggregationKey, $watcher->buildAggregationKey());
    }

    /**
     * @test
     */
    public function shouldReturnWatcherAsArray()
    {
        $this->createWatcherEmailAction();
        $this->createAggregator();

        $watcher = Watcher::create(
            $this->name,
            $this->event,
            $this->threshold,
            $this->aggregator,
            $this->action
        );

        $this->assertEquals(
            [
                '_id',
                'name',
                'threshold',
                'aggregator',
                'action'
            ],
            array_keys($watcher->toArray())
        );
    }

    /**
     * @test
     * @dataProvider getInvalidWatcherParameters
     *
     * @param array $invalidParameters
     */
    public function shouldThrowInvalidParameterExceptionWhenWatcherIsInvalid(array $invalidParameters)
    {
        $this->setExpectedException(InvalidParameterException::class);

        Watcher::create(
            $invalidParameters['name'],
            $invalidParameters['event'],
            $invalidParameters['threshold'],
            $invalidParameters['aggregator'],
            $invalidParameters['action']
        );
    }

    /**
     * @return array
     */
    public function getInvalidWatcherParameters()
    {
        $this->createWatcherEmailAction();
        $this->createAggregator();

        return [
            [
                [
                    'name' => '',
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => false,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => true,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => null,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => [],
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => 0,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => 1234,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => false,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => true,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => [],
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 0,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 1234,
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'threshold' => $this->threshold,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => false,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => true,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => [],
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => 0,
                    'aggregator' => $this->aggregator,
                    'action' => $this->action
                ]
            ]
        ];
    }
}
