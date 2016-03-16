<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler;

/**
 * Class WatcherNullActionHandler
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler
 */
class WatcherNullActionHandler implements WatcherActionHandler
{
    /**
     * @param string $message
     */
    public function handle($message)
    {
        // noop
    }
}