<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher;

use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherCounters;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class WatcherCountersTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Event\Watcher
 */
class WatcherCountersTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $key = 'test';

    /**
     * @test
     */
    public function shouldIncreaseCounter()
    {
        $counters = WatcherCounters::init();
        $currentCounter = $counters->getCounter($this->key);

        $this->assertEquals($currentCounter + 1, $counters->increaseCounter($this->key));

    }

    /**
     * @test
     */
    public function shouldReturnCounter()
    {
        $counter = 100;
        $counters = new WatcherCounters([$this->key => $counter]);

        $this->assertEquals($counter, $counters->getCounter($this->key));
    }

    /**
     * @test
     */
    public function shouldReturnAllCounters()
    {
        $counters = WatcherCounters::init();

        $counters->increaseCounter('key1');
        $counters->increaseCounter('key1');
        $counters->increaseCounter('key1');
        $counters->increaseCounter('key2');
        $counters->increaseCounter('key2');
        $counters->increaseCounter('key3');

        $this->assertEquals(
            [
                'key1' => 3,
                'key2' => 2,
                'key3' => 1
            ],
            $counters->getCounters()
        );
    }

    /**
     * @test
     */
    public function shouldClearCounters()
    {
        $counters = WatcherCounters::init();

        $counters->increaseCounter('key1');
        $counters->increaseCounter('key1');
        $counters->increaseCounter('key1');
        $counters->increaseCounter('key2');
        $counters->increaseCounter('key2');
        $counters->increaseCounter('key3');

        $this->assertEquals(3, $counters->getCounter('key1'));
        $this->assertEquals(2, $counters->getCounter('key2'));
        $this->assertEquals(1, $counters->getCounter('key3'));

        $counters->clear();

        $this->assertEquals(0, $counters->getCounter('key1'));
        $this->assertEquals(0, $counters->getCounter('key2'));
        $this->assertEquals(0, $counters->getCounter('key1'));
        $this->assertEquals([], $counters->getCounters());
    }

    /**
     * @test
     * @dataProvider getInvalidCounterKey
     *
     * @param mixed $invalidKey
     */
    public function shouldThrowInvalidParameterExceptionIfCounterKeyIsInvalid($invalidKey)
    {
        $this->setExpectedException(InvalidParameterException::class);

        $counters = WatcherCounters::init();
        $counters->getCounter($invalidKey);
    }

    /**
     * @return array
     */
    public function getInvalidCounterKey()
    {
        return [
            [
                [
                    true
                ]
            ],
            [
                [
                    false
                ]
            ],
            [
                [
                    null
                ]
            ],
            [
                [
                    []
                ]
            ],
            [
                [
                    0
                ]
            ],
            [
                [
                    ''
                ]
            ]
        ];
    }
}
