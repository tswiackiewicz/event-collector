<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender;

use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorAppender;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\CollectorNullAppender;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class CollectorNullAppenderTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender
 */
class CollectorNullAppenderTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateCollectorNullAppender()
    {
        $appender = CollectorNullAppender::create();

        $this->assertCollectorAppender($appender);
    }

    /**
     * @param CollectorNullAppender $appender
     */
    private function assertCollectorAppender(CollectorNullAppender $appender)
    {
        $this->assertEquals(CollectorAppender::NULL_APPENDER, $appender->getType());
        $this->assertEquals([], $appender->getParameters());
        $this->assertEquals(
            [
                'type' => CollectorAppender::NULL_APPENDER
            ],
            $appender->toArray()
        );
    }
}
