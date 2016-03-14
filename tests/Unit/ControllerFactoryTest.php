<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit;

use TSwiackiewicz\EventsCollector\ControllerFactory;
use TSwiackiewicz\EventsCollector\Counters\InMemoryCounters;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherController;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Settings\InMemorySettings;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ControllerFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit
 */
class ControllerFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateCollectorController()
    {
        $factory = $this->createControllerFactory();
        $controller = $factory->create(CollectorController::class);

        $this->assertInstanceOf(CollectorController::class, $controller);
    }

    /**
     * @return ControllerFactory
     */
    private function createControllerFactory()
    {
        return new ControllerFactory(
            new InMemorySettings(),
            new InMemoryCounters()
        );
    }

    /**
     * @test
     */
    public function shouldCreateWatcherController()
    {
        $factory = $this->createControllerFactory();
        $controller = $factory->create(WatcherController::class);

        $this->assertInstanceOf(WatcherController::class, $controller);
    }

    /**
     * @test
     */
    public function shouldCreateEventController()
    {
        $factory = $this->createControllerFactory();
        $controller = $factory->create(EventController::class);

        $this->assertInstanceOf(EventController::class, $controller);
    }

    /**
     * @test
     */
    public function shouldThrowUnknownTypeExceptionWhenGivenControllerClassNameIsUnknown()
    {
        $this->setExpectedException(UnknownTypeException::class);

        $factory = $this->createControllerFactory();
        $factory->create(__CLASS__);
    }
}