<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler;

/**
 * Interface WatcherActionHandler
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler
 */
interface WatcherActionHandler
{
    /**
     * @param string $message
     */
    public function handle($message);
}