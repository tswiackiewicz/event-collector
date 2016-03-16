<?php
namespace TSwiackiewicz\EventsCollector\Tests\Integration;

use FastRoute\DataGenerator\GroupCountBased as FastRouteDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as FastRouteGroupCountBasedDispatcher;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std as FastRouteStdRouteParser;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Dispatcher;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;
use TSwiackiewicz\EventsCollector\Tests\FakeController;
use TSwiackiewicz\EventsCollector\Tests\FakeControllerFactory;

/**
 * Class DispatcherTest
 * @package TSwiackiewicz\EventsCollector\Tests\Integration
 */
class DispatcherTest extends BaseTestCase
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
    public function shouldDumpSettings()
    {
        $dumpFilePath = __DIR__ . '/settings_dump.yml';

        $dispatcher = $this->createDispatcher();
        $dispatcher->dumpSettings($dumpFilePath);

        $this->assertEquals(
            $this->settings,
            Yaml::parse(
                file_get_contents($dumpFilePath)
            )
        );

        unlink($dumpFilePath);
    }

    /**
     * @return Dispatcher
     */
    private function createDispatcher()
    {
        $routes = $this->createRoutesCollection();

        $file = $this->generateSettingsFile('config.yml');
        $settings = InMemorySettings::loadFromFile($file);

        return new Dispatcher(
            new FastRouteGroupCountBasedDispatcher(
                $routes->getRoutes()
            ),
            new FakeControllerFactory(
                $settings,
                new InMemoryCounters()
            )
        );
    }

    /**
     * @return RoutesCollection
     */
    private function createRoutesCollection()
    {
        $routes = new FastRouteCollector(
            new FastRouteStdRouteParser(),
            new FastRouteDataGenerator()
        );

        $routes->addRoute(
            'GET',
            '/success/',
            [FakeController::class, 'successfulCallback']
        );
        $routes->addRoute(
            'PUT',
            '/invalid_callback_response/',
            [FakeController::class, 'invalidCallback']
        );
        $routes->addRoute(
            'GET',
            '/invalid_controller/',
            ['throwableCallback']
        );
        $routes->addRoute(
            'POST',
            '/invalid_json_payload/',
            [FakeController::class, 'successfulCallback']
        );

        return new RoutesCollection($routes);
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
}
