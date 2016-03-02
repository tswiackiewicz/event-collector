<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Action;

use TSwiackiewicz\EventsCollector\Action\Action;
use TSwiackiewicz\EventsCollector\Action\ActionTarget;
use TSwiackiewicz\EventsCollector\Action\Email\EmailActionTarget;
use TSwiackiewicz\EventsCollector\Action\Exception\InvalidActionParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ActionTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Action
 */
class ActionTest extends BaseTestCase
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
     * @var string[]
     */
    private $aggregationKey = ['field1', 'field2'];

    /**
     * @var ActionTarget
     */
    private $target;

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
    public function shouldCreateValidAction()
    {
        $this->createEmailActionTarget();

        $action = Action::create(
            $this->name,
            $this->event,
            $this->threshold,
            $this->aggregationKey,
            $this->target
        );

        $this->assertAction($action);
    }

    private function createEmailActionTarget()
    {
        $this->target = EmailActionTarget::create(
            [
                EmailActionTarget::TO_ADDRESS_PARAMETER => $this->toAddress,
                EmailActionTarget::SUBJECT_PARAMETER => $this->subject
            ]
        );
    }

    /**
     * @param Action $action
     */
    private function assertAction(Action $action)
    {
        $this->assertEquals($this->name, $action->getName());
        $this->assertEquals($this->event, $action->getEvent());
        $this->assertEquals($this->threshold, $action->getThreshold());
        $this->assertEquals($this->aggregationKey, $action->getAggregationKey());
        $this->assertEquals($this->target, $action->getTarget());
    }

    /**
     * @test
     */
    public function shouldReturnActionAsArray()
    {
        $this->createEmailActionTarget();

        $action = Action::create(
            $this->name,
            $this->event,
            $this->threshold,
            $this->aggregationKey,
            $this->target
        );

        $this->assertEquals(
            [
                '_id',
                'name',
                'threshold',
                'aggregation_key',
                'target'
            ],
            array_keys($action->toArray())
        );
    }

    /**
     * @test
     * @dataProvider getInvalidActionParameters
     *
     * @param array $invalidParameters
     */
    public function shouldThrowInvalidActionParameterExceptionIfActionIsInvalid(array $invalidParameters)
    {
        $this->setExpectedException(InvalidActionParameterException::class);

        Action::create(
            $invalidParameters['name'],
            $invalidParameters['event'],
            $invalidParameters['threshold'],
            $invalidParameters['aggregation_key'],
            $invalidParameters['target']
        );
    }

    /**
     * @return array
     */
    public function getInvalidActionParameters()
    {
        $this->createEmailActionTarget();

        return [
            [
                [
                    'name' => '',
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => false,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => true,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => null,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => [],
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => 0,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => 1234,
                    'event' => $this->event,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => false,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => true,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => [],
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 0,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => 1234,
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => '',
                    'threshold' => $this->threshold,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => false,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => true,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => [],
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ],
            [
                [
                    'name' => $this->name,
                    'event' => $this->event,
                    'threshold' => 0,
                    'aggregation_key' => $this->aggregationKey,
                    'target' => $this->target
                ]
            ]
        ];
    }
}
