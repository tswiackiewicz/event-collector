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

    /**
     * @test
     */
    public function shouldCreateActionFromArray()
    {
        $factory = $this->createActionFactory();

        $action = $factory->createFromArray(
            'test_event',
            [
                '_id' => '3a942a2b-04a0-4d23-9de7-1b433566ef05',
                'name' => 'test_action',
                'threshold' => 100,
                'aggregation_key' => [],
                'target' => [
                    'type' => 'email',
                    'to' => 'user@domain.com',
                    'subject' => 'Test subject'
                ]
            ]
        );

        $this->assertInstanceOf(Action::class, $action);
    }

    /**
     * @test
     */
    public function shouldThrowInvalidActionParameterExceptionIfActionTypeIsNotDefinedWhenCreatingFromArray()
    {
        $this->setExpectedException(InvalidActionParameterException::class);

        $factory = $this->createActionFactory();

        $factory->createFromArray(
            'test_event',
            [
                'target' => []
            ]
        );
    }

    /**
     * @test
     */
    public function shouldThrowUnknownActionTypeExceptionIfActionTypeIsUnknownWhenCreatingFromArray()
    {
        $this->setExpectedException(UnknownActionTypeException::class);

        $factory = $this->createActionFactory();

        $factory->createFromArray(
            'test_event',
            [
                'target' => [
                    'type' => 'unknown_type'
                ]
            ]
        );
    }
}
