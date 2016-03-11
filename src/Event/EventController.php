<?php
namespace TSwiackiewicz\EventsCollector\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Http\JsonException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

/**
 * Class EventController
 * @package TSwiackiewicz\EventsCollector\Event
 */
class EventController
{
    /**
     * @var EventService
     */
    private $service;

    /**
     * @param EventService $service
     */
    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getAllEvents()
    {
        try {

            $allEvents = $this->service->getEvents();

            $events = [];
            foreach ($allEvents as $event) {
                $events[] = [
                    '_id' => $event->getId(),
                    'type' => $event->getType()
                ];
            }

        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            $events,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEvent(Request $request)
    {
        try {

            $event = $this->service->getEvent($request->query->get('event'));

        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            $event->toArray(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerEvent(Request $request)
    {
        try {

            $payload = RequestPayload::fromJson($request->getContent());
            $event = Event::create($payload->getValue('type'));

            $this->service->registerEvent($event);

        } catch (AlreadyRegisteredException $registered) {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $registered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            [
                '_id' => $event->getId()
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterEvent(Request $request)
    {
        try {

            $this->service->unregisterEvent($request->request->get('event'));

        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

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
    public function collectEvent(Request $request)
    {
        try {

            $event = $this->service->getEvent($request->request->get('event'));

            $this->service->collectEvent($event, $request->getContent());

        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            [
                '_id' => $event->getId()
            ],
            JsonResponse::HTTP_OK
        );
    }
}