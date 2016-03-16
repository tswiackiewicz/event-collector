<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherNullAction;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherNullActionTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action
 */
class WatcherNullActionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateWatcherNullAction()
    {
        $action = WatcherNullAction::create();

        $this->assertAction($action);
    }

    /**
     * @param WatcherAction $action
     */
    private function assertAction(WatcherAction $action)
    {
        $this->assertEquals(WatcherAction::NULL_ACTION, $action->getType());
        $this->assertEquals([], $action->getParameters());
        $this->assertEquals(
            [
                'type' => WatcherAction::NULL_ACTION
            ],
            $action->toArray()
        );
    }
}
