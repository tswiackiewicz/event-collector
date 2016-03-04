<?php
namespace TSwiackiewicz\EventsCollector\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Collector\Handler\CollectorHandlerFactory;
use TSwiackiewicz\EventsCollector\Controller;
use TSwiackiewicz\EventsCollector\Event\Exception\NotRegisteredCollectorsException;
use TSwiackiewicz\EventsCollector\Http\JsonException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class EventController
 * @package TSwiackiewicz\EventsCollector\Event
 */
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
     * @param Request $request
     * @return JsonResponse
     * @throws NotRegisteredCollectorsException
     */
    public function collectEvent(Request $request)
    {
        $event = $this->configuration->getEventType(
            $request->request->get('event')
        );
        $payload = $request->getContent();

        $collectors = $event->getCollectors();
        if (empty($collectors)) {
            throw new NotRegisteredCollectorsException(
                JsonResponse::HTTP_NOT_FOUND,
                'Collectors are not registered for event type `' . $event->getType() . '`'
            );
        }

        try {
            $collectedEventUuid = Uuid::generate();
            $factory = new CollectorHandlerFactory();
            foreach ($collectors as $collector) {
                $handler = $factory->createFromCollector($collector);
                $handler->handle($collectedEventUuid, $payload);
            }
        }
        catch (\Exception $e)
        {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            [
                '_id' => $collectedEventUuid->getUuid()
            ],
            JsonResponse::HTTP_OK
        );
    }
}