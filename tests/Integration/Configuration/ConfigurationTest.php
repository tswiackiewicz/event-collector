<?php
namespace TSwiackiewicz\EventsCollector\Tests\Integration\Configuration;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ConfigurationTest
 * @package TSwiackiewicz\EventsCollector\Tests\Integration\Configuration
 */
class ConfigurationTest extends BaseTestCase
{
    /**
     * @var array
     */
    private $configuration = [
        [
            '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
            'type' => 'new_event-1',
            'collectors' => [],
            'actions' => []
        ],
        [
            '_id' => '1f2a4477-1e3a-4b25-9973-0fbab380af49',
            'type' => 'new_event-2',
            'collectors' => [],
            'actions' => []
        ]
    ];

    /**
     * @test
     */
    public function shouldLoadConfigurationFromFile()
    {
        $file = $this->generateConfigurationFile('config.yml');

        $configuration = Configuration::loadFromFile($file);

        $this->assertConfiguration($configuration);
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function generateConfigurationFile($fileName)
    {
        vfsStream::setup(
            'test',
            null,
            [
                $fileName => Yaml::dump($this->configuration)
            ]
        );

        return vfsStream::url('test' . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @param Configuration $configuration
     */
    private function assertConfiguration(Configuration $configuration)
    {
        $eventTypes = [];

        $allEvents = $configuration->getAllEventTypes();
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
    public function shouldLoadConfigurationFromConfigFile()
    {
        $file = $this->generateConfigurationFile('config.yml');
        putenv('CONFIGURATION_DUMP_FILE_PATH=');
        putenv('CONFIGURATION_FILE_PATH=' . $file);

        $configuration = Configuration::loadFromFile();

        $this->assertConfiguration($configuration);
    }

    /**
     * @test
     */
    public function shouldLoadConfigurationFromConfigDumpFile()
    {
        $file = $this->generateConfigurationFile('config.yml.dump');
        putenv('CONFIGURATION_FILE_PATH=');
        putenv('CONFIGURATION_DUMP_FILE_PATH=' . $file);

        $configuration = Configuration::loadFromFile();

        $this->assertConfiguration($configuration);
    }

    /**
     * @test
     */
    public function shouldDumpConfigurationToFile()
    {
        $file = $this->generateConfigurationFile('config.yml');
        $dumpFilePath = __DIR__ . '/config_dump.yml';

        $configuration = Configuration::loadFromFile($file);
        $configuration->dump($dumpFilePath);

        $this->assertEquals(
            $this->configuration,
            Yaml::parse(
                file_get_contents($dumpFilePath)
            )
        );

        unlink($dumpFilePath);
    }
}
