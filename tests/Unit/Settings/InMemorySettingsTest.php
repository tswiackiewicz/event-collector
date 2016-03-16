<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Settings;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class InMemorySettingsTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Settings
 */
class InMemorySettingsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldThrowInvalidParameterExceptionIfEventTypeIsEmpty()
    {
        $this->setExpectedException(InvalidParameterException::class);

        $settings = new InMemorySettings();
        $settings->getEvent('');
    }
}
