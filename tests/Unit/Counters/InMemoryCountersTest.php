<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Counters;

use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class InMemoryCountersTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Counters
 */
class InMemoryCountersTest extends BaseTestCase
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
        $counters = new InMemoryCounters();
        $currentCounter = $counters->getCounter($this->key);

        $this->assertEquals($currentCounter + 1, $counters->increaseCounter($this->key));
    }

    /**
     * @test
     */
    public function shouldReturnCounter()
    {
        $counter = 100;
        $counters = new InMemoryCounters([$this->key => $counter]);

        $this->assertEquals($counter, $counters->getCounter($this->key));
    }

    /**
     * @test
     */
    public function shouldReturnAllCounters()
    {
        $counters = new InMemoryCounters();

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
        $counters = new InMemoryCounters();

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

        $counters = new InMemoryCounters();
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
