<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Configuration;

use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
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
    public function shouldThrowInvalidParameterExceptionIfEventTypeIsEmpty()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $configuration = new Configuration();
        $configuration->getEventType('');
    }
}
