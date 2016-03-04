<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Configuration;

use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Event\Exception\InvalidEventParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ConfigurationTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Configuration
 */
class ConfigurationTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldThrowInvalidEventParameterExceptionIfEventTypeIsEmpty()
    {
        $this->setExpectedException(InvalidEventParameterException::class);

        $configuration = new Configuration();
        $configuration->getEventType('');
    }
}
