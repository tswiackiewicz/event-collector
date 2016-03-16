<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector\Appender;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CollectorNullAppender
 * @package TSwiackiewicz\EventsCollector\Event\Collector\Appender
 */
class CollectorNullAppender extends CollectorAppender
{
    /**
     * @return CollectorNullAppender
     */
    public static function create()
    {
        return new static(self::NULL_APPENDER, new ParameterBag([]));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::NULL_APPENDER
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