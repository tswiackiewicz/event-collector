<?php
namespace TSwiackiewicz\EventsCollector\Tests\Integration\Settings;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Settings\Settings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class InMemorySettingsTest
 * @package TSwiackiewicz\EventsCollector\Tests\Integration\Settings
 */
class InMemorySettingsTest extends BaseTestCase
{
    /**
     * @var array
     */
    private $settings = [
        [
            '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
            'type' => 'new_event-1',
            'collectors' => [],
            'watchers' => []
        ],
        [
            '_id' => '1f2a4477-1e3a-4b25-9973-0fbab380af49',
            'type' => 'new_event-2',
            'collectors' => [],
            'watchers' => []
        ]
    ];

    /**
     * @test
     */
    public function shouldLoadSettingsFromFile()
    {
        $file = $this->generateSettingsFile('config.yml');

        $settings = InMemorySettings::loadFromFile($file);

        $this->assertSettings($settings);
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function generateSettingsFile($fileName)
    {
        vfsStream::setup(
            'test',
            null,
            [
                $fileName => Yaml::dump($this->settings)
            ]
        );

        return vfsStream::url('test' . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @param Settings $settings
     */
    private function assertSettings(Settings $settings)
    {
        $eventTypes = [];

        $allEvents = $settings->getEvents();
        foreach ($allEvents as $event) {
            $eventTypes[] = $event->getType();
        }

        $this->assertEquals(
            [
                'new_event-1',
                'new_event-2'
            ],
            $eventTypes
        );
    }

    /**
     * @test
     */
    public function shouldLoadSettingsFromConfigFile()
    {
        $file = $this->generateSettingsFile('config.yml');
        putenv('CONFIGURATION_DUMP_FILE_PATH=');
        putenv('CONFIGURATION_FILE_PATH=' . $file);

        $settings = InMemorySettings::loadFromFile();

        $this->assertSettings($settings);
    }

    /**
     * @test
     */
    public function shouldLoadSettingsFromConfigDumpFile()
    {
        $file = $this->generateSettingsFile('config.yml.dump');
        putenv('CONFIGURATION_FILE_PATH=');
        putenv('CONFIGURATION_DUMP_FILE_PATH=' . $file);

        $settings = InMemorySettings::loadFromFile();

        $this->assertSettings($settings);
    }

    /**
     * @test
     */
    public function shouldDumpSettingsToFile()
    {
        $file = $this->generateSettingsFile('config.yml');
        $dumpFilePath = __DIR__ . '/config_dump.yml';

        $settings = InMemorySettings::loadFromFile($file);
        $settings->dump($dumpFilePath);

        $this->assertEquals(
            $this->settings,
            Yaml::parse(
                file_get_contents($dumpFilePath)
            )
        );

        unlink($dumpFilePath);
    }
}
