<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Action;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class WatcherNullAction
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Action
 */
class WatcherNullAction extends WatcherAction
{
    /**
     * @return WatcherNullAction
     */
    public static function create()
    {
        return new static(self::NULL_ACTION, new ParameterBag([]));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => $this->getType()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validateParameters()
    {
        // noop
    }
}