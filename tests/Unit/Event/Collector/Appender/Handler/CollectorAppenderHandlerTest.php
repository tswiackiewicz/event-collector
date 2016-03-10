<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender\Handler;

use Psr\Log\NullLogger;
use TSwiackiewicz\EventsCollector\Event\Collector\Appender\Handler\CollectorAppenderHandler;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class CollectorAppenderHandlerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Collector\Appender\Handler
 */
class CollectorAppenderHandlerTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldHandleGivenPayload()
    {
        $uuid = Uuid::generate()->getUuid();
        $payload = json_encode(['key' => 'value']);

        $handler = new CollectorAppenderHandler(new NullLogger());
        $handler->handle($uuid, $payload);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionWhenGivenPayloadIsNotProperJsonString()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $uuid = Uuid::generate()->getUuid();
        $payload = '中华人民共和国';

        $handler = new CollectorAppenderHandler(new NullLogger());
        $handler->handle($uuid, $payload);
    }
}
