<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Action;

use TSwiackiewicz\EventsCollector\Action\Action;
use TSwiackiewicz\EventsCollector\Action\ActionFactory;
use TSwiackiewicz\EventsCollector\Action\Exception\InvalidActionParameterException;
use TSwiackiewicz\EventsCollector\Action\Exception\UnknownActionTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ActionFactoryTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Action
 */
class ActionFactoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldCreateAction()
    {
        $factory = $this->createActionFactory();

        $action = $factory->create(
            'test_event',
            '{"name":"test_action","threshold":100,"aggregation_key":[],"target":{"type":"email","to":"user@domain.com","subject":"Test subject"}}'
        );

        $this->assertInstanceOf(Action::class, $action);
    }

    /**
     * @return ActionFactory
     */
    private function createActionFactory()
    {
        return new ActionFactory();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidActionParameterExceptionIfActionTypeIsNotDefined()
    {
        $this->setExpectedException(InvalidActionParameterException::class);

        $factory = $this->createActionFactory();

        $factory->create(
            'test_event',
            '{"target":[]}'
        );
    }

    /**
     * @test
     */
    public function shouldThrowUnknownActionTypeExceptionIfActionTypeIsUnknown()
    {
        $this->setExpectedException(UnknownActionTypeException::class);

        $factory = $this->createActionFactory();

        $factory->create(
            'test_event',
            '{"target":{"type":"unknown_type"}}'
        );
    }
}
