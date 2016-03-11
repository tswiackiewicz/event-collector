<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action\Handler;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandler;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandlerFactory;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherEmailAction;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherActionHandlerFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action\Handler
 */
class WatcherActionHandlerFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateWatcherActionHandlerFromGivenWatcherAction()
    {
        $action = WatcherEmailAction::create(
            [
                WatcherEmailAction::TO_ADDRESS_PARAMETER => 'test@domain.com',
                WatcherEmailAction::SUBJECT_PARAMETER => 'Test'
            ]
        );

        $factory = new WatcherActionHandlerFactory();
        $handler = $factory->createFromWatcherAction($action);

        $this->assertInstanceOf(WatcherActionHandler::class, $handler);
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenGivenWatcherActionIsUnknownType()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $action = $this->createWatcherEmailActionWithUnknownType();

        $factory = new WatcherActionHandlerFactory();
        $factory->createFromWatcherAction($action);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|WatcherAction
     */
    private function createWatcherEmailActionWithUnknownType()
    {
        $action = $this->getMockBuilder(WatcherAction::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getType'
                ]
            )
            ->getMockForAbstractClass();
        $action->expects($this->once())->method('getType')->willReturn('unknown_type');

        return $action;
    }
}
