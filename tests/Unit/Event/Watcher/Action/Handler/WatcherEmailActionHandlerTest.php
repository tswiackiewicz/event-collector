<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action\Handler;

use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherActionHandler;
use TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler\WatcherEmailActionHandler;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherEmailActionHandlerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher\Action\Handler
 */
class WatcherEmailActionHandlerTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateWatcherEmailActionHandler()
    {
        $handler = new WatcherEmailActionHandler('test@domain.com', 'Test');

        $this->assertInstanceOf(WatcherActionHandler::class, $handler);
    }

    /**
     * @test
     * @dataProvider getInvalidHandler
     *
     * @param array $invalidHandler
     */
    public function shouldThrowInvalidParameterExceptionWhenGivenHandlerIsInvalid(array $invalidHandler)
    {
        $this->setExpectedException(InvalidParameterException::class);

        new WatcherEmailActionHandler($invalidHandler[0], $invalidHandler[1]);
    }

    /**
     * @return array
     */
    public function getInvalidHandler()
    {
        return [
            [
                [
                    'test',
                    'Test'
                ]
            ],
            [
                [
                    'test@',
                    'Test'
                ]
            ],
            [
                [
                    '@test',
                    'Test'
                ]
            ],
            [
                [
                    'test@domain.com',
                    ''
                ]
            ],
            [
                [
                    'test@domain.com',
                    []
                ]
            ],
            [
                [
                    'test@domain.com',
                    false
                ]
            ],
            [
                [
                    'test@domain.com',
                    true
                ]
            ],
            [
                [
                    'test@domain.com',
                    null
                ]
            ],
            [
                [
                    'test@domain.com',
                    0
                ]
            ],
            [
                [
                    'test@domain.com',
                    -123
                ]
            ]
        ];
    }
}
