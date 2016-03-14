<?php
namespace TSwiackiewicz\EventsCollector;

use TSwiackiewicz\EventsCollector\Counters\Counters;
use TSwiackiewicz\EventsCollector\Event\Collector\CollectorController;
use TSwiackiewicz\EventsCollector\Event\EventController;
use TSwiackiewicz\EventsCollector\Event\Watcher\WatcherController;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;
use TSwiackiewicz\EventsCollector\Settings\Settings;

/**
 * Class ControllerFactory
 * @package TSwiackiewicz\EventsCollector
 */
class ControllerFactory
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Counters
     */
    private $counters;

    /**
     * @param Settings $settings
     * @param Counters $counters
     */
    public function __construct(Settings $settings, Counters $counters)
    {
        $this->settings = $settings;
        $this->counters = $counters;
    }

    /**
     * @param string $controllerClassName
     * @return CollectorController|EventController|WatcherController
     * @throws UnknownTypeException
     */
    public function create($controllerClassName)
    {
        switch ($controllerClassName) {
            case CollectorController::class:
                return CollectorController::create($this->settings);

            case WatcherController::class:
                return WatcherController::create($this->settings, $this->counters);

            case EventController::class:
                return EventController::create($this->settings, $this->counters);
        }

        throw new UnknownTypeException('Unknown controller class name');
    }

    /**
     * @param string $file
     */
    public function dumpSettings($file)
    {
        $this->settings->dump($file);
    }
}