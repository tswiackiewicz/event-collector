<?php
namespace TSwiackiewicz\EventsCollector\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Controller;

/**
 * Class ActionController
 * @package TSwiackiewicz\EventsCollector\Action
 */
class ActionController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllEventActions(Request $request)
    {
        $actions = [];
        $eventType = $request->query->get('event');

        $eventActions = $this->configuration->getEventActions($eventType);
        foreach ($eventActions as $action) {
            $actions[] = [
                '_id' => $action->getId(),
                'name' => $action->getName()
            ];
        }

        return new JsonResponse(
            $actions,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventAction(Request $request)
    {
        $action = $this->configuration->getEventAction(
            $request->query->get('event'),
            $request->query->get('action')
        );

        return new JsonResponse(
            $action->toArray(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterEventAction(Request $request)
    {
        $this->configuration->unregisterEventAction(
            $request->request->get('event'),
            $request->request->get('action')
        );

        return new JsonResponse(
            [
                'acknowledged' => true
            ],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerEventAction(Request $request)
    {
        $eventType = $request->request->get('event');

        $factory = new ActionFactory();
        $action = $factory->create(
            $eventType,
            $request->getContent()
        );

        $this->configuration->registerEventAction($eventType, $action);

        return new JsonResponse(
            [
                '_id' => $action->getId()
            ],
            JsonResponse::HTTP_CREATED
        );
    }
}