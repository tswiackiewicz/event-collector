<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Action\ActionController;
use TSwiackiewicz\EventsCollector\Action\Exception\ActionAlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Action\Exception\NotRegisteredActionException;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Event\Event;
use TSwiackiewicz\EventsCollector\Event\Exception\NotRegisteredEventTypeException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class ActionControllerTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Action
 */
class ActionControllerTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $event = 'test_event';

    /**
     * @var string
     */
    private $action = 'test_action';

    /**
     * @var string
     */
    private $payload = '{"name":"test_action","threshold":100,"aggregation_key":[],"target":{"type":"email","to":"user@domain.com","subject":"Test subject"}}';

    /**
     * @test
     */
    public function shouldReturnAllEventActionsAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createActionController();
        $controller->registerEventAction($request);

        $response = $controller->getAllEventActions($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return Request
     */
    private function createRequest()
    {
        return new Request(
            [
                'event' => $this->event,
                'action' => $this->action
            ],
            [
                'event' => $this->event,
                'action' => $this->action
            ],
            [],
            [],
            [],
            [],
            $this->payload
        );
    }

    /**
     * @return ActionController
     */
    private function createActionController()
    {
        $configuration = new Configuration();
        $configuration->registerEventType(
            Event::create($this->event)
        );

        return new ActionController(
            $configuration
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenReturnAllActionsForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createActionControllerWithoutEventRegistered();
        $controller->getAllEventActions(
            $this->createRequest()
        );
    }

    /**
     * @return ActionController
     */
    private function createActionControllerWithoutEventRegistered()
    {
        return new ActionController(
            new Configuration()
        );
    }

    /**
     * @test
     */
    public function shouldReturnEventActionAsJsonResponse()
    {
        $request = $this->createRequest();

        $controller = $this->createActionController();
        $controller->registerEventAction($request);

        $response = $controller->getEventAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenReturnActionForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createActionControllerWithoutEventRegistered();
        $controller->getEventAction(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredActionExceptionWhenReturnEventActionForNotRegisteredAction()
    {
        $this->setExpectedException(NotRegisteredActionException::class);

        $controller = $this->createActionController();
        $controller->getEventAction(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldUnregisterEventAction()
    {
        $request = $this->createRequest();

        $controller = $this->createActionController();
        $controller->registerEventAction($request);

        $response = $controller->unregisterEventAction($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenUnregisterActionForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createActionControllerWithoutEventRegistered();
        $controller->unregisterEventAction(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredActionExceptionWhenUnregisterEventActionForNotRegisteredAction()
    {
        $this->setExpectedException(NotRegisteredActionException::class);

        $controller = $this->createActionController();
        $controller->unregisterEventAction(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldRegisterEventAction()
    {
        $controller = $this->createActionController();
        $response = $controller->registerEventAction(
            $this->createRequest()
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldThrowNotRegisteredEventTypeExceptionWhenRegisterActionForNotRegisteredEvent()
    {
        $this->setExpectedException(NotRegisteredEventTypeException::class);

        $controller = $this->createActionControllerWithoutEventRegistered();
        $controller->registerEventAction(
            $this->createRequest()
        );
    }

    /**
     * @test
     */
    public function shouldThrowActionAlreadyRegisteredExceptionWhenRegisterAlreadyRegisteredAction()
    {
        $this->setExpectedException(ActionAlreadyRegisteredException::class);

        $request = $this->createRequest();

        $controller = $this->createActionController();
        $controller->registerEventAction($request);
        $controller->registerEventAction($request);
    }
}
