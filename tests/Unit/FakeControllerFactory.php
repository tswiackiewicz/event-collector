<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit;

use TSwiackiewicz\EventsCollector\ControllerFactory;
use TSwiackiewicz\EventsCollector\Exception\UnknownTypeException;

/**
 * Class FakeControllerFactory
 * @package TSwiackiewicz\EventsCollector\Tests\Unit
 */
class FakeControllerFactory extends ControllerFactory
{
    /**
     * {@inheritdoc}
     */
    public function create($controllerClassName)
    {
        switch ($controllerClassName) {
            case FakeController::class:
                return new FakeController();
        }

        throw new UnknownTypeException('Unknown controller class name');
    }

}