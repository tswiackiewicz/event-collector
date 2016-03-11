<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherAction;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\WatcherEmailAction;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;

/**
 * Class WatcherActionHandlerFactory
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler
 */
class WatcherActionHandlerFactory
{
    /**
     * @param WatcherAction $action
     * @return WatcherActionHandler
     * @throws UnknownTypeException
     */
    public function createFromWatcherAction(WatcherAction $action)
    {
        $actionType = $action->getType();

        switch ($actionType) {
            case WatcherAction::EMAIL_ACTION:
                return new WatcherEmailActionHandler(
                    $action->getParameter(WatcherEmailAction::TO_ADDRESS_PARAMETER),
                    $action->getParameter(WatcherEmailAction::SUBJECT_PARAMETER)
                );
        }

        throw new UnknownTypeException('Unknown watcher action type');
    }
}