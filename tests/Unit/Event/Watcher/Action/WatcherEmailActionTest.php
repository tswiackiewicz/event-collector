<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherEmailAction;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherEmailActionTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action
 */
class WatcherEmailActionTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $toAddress = 'user@domain.com';

    /**
     * @var string
     */
    private $subject = 'Test subject';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @test
     */
    public function shouldCreateWatcherEmailAction()
    {
        $this->parameters = [
            WatcherEmailAction::TO_ADDRESS_PARAMETER => $this->toAddress,
            WatcherEmailAction::SUBJECT_PARAMETER => $this->subject
        ];

        $action = WatcherEmailAction::create($this->parameters);

        $this->assertAction($action);
    }

    /**
     * @param WatcherEmailAction $action
     */
    private function assertAction(WatcherEmailAction $action)
    {
        $this->assertEquals(WatcherEmailAction::EMAIL_ACTION, $action->getType());
        $this->assertEquals($this->parameters, $action->getParameters());
        $this->assertEquals($this->toAddress, $action->getParameter(WatcherEmailAction::TO_ADDRESS_PARAMETER));
        $this->assertEquals($this->subject, $action->getParameter(WatcherEmailAction::SUBJECT_PARAMETER));
    }

    /**
     * @test
     * @dataProvider getInvalidWatcherEmailAction
     *
     * @param array $invalidEmailActionParameters
     */
    public function shouldThrowInvalidParameterExceptionWhenWatcherEmailActionIsInvalid(
        array $invalidEmailActionParameters
    ) {
        $this->setExpectedException(InvalidParameterException::class);

        WatcherEmailAction::create($invalidEmailActionParameters);
    }

    /**
     * @return array
     */
    public function getInvalidWatcherEmailAction()
    {
        return [
            [
                [
                    WatcherEmailAction::SUBJECT_PARAMETER => $this->subject
                ]
            ],
            [
                [
                    WatcherEmailAction::TO_ADDRESS_PARAMETER => $this->toAddress
                ]
            ]
        ];
    }
}
