<?php
namespace TSwiackiewicz\EventsCollector\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Controller;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

class EventController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getAllEventTypes()
    {
        $eventTypes = [];

        $allEventTypes = $this->configuration->getAllEventTypes();
        foreach ($allEventTypes as $eventType) {
            $eventTypes[] = [
                '_id' => $eventType->getId(),
                'type' => $eventType->getType()
            ];
        }

        return new JsonResponse(
            $eventTypes,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventType(Request $request)
    {
        $eventType = $this->configuration->getEventType(
            $request->query->get('event')
        );

        return new JsonResponse(
            $eventType->toArray(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterEventType(Request $request)
    {
        $this->configuration->unregisterEventType(
            $request->request->get('event')
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
    public function registerEventType(Request $request)
    {
        $payload = RequestPayload::fromJson($request->getContent());
        $eventType = Event::create(
            $payload->getValue('type')
        );

        $this->configuration->registerEventType($eventType);

        return new JsonResponse(
            [
                '_id' => $eventType->getId()
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @codeCoverageIgnore
     * @param Request $request
     */
    public function collectEvent(Request $request)
    {
        $eventType = $request->request->get('event');
        $payload = RequestPayload::fromJson($request->getContent());
    }

}