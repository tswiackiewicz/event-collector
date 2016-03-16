<?php
namespace TSwiackiewicz\EventsCollector\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Controller;
use TSwiackiewicz\EventsCollector\Counters\Counters;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidControllerDefinitionException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Http\JsonErrorResponse;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;
use TSwiackiewicz\EventsCollector\Settings\Settings;

/**
 * Class EventController
 * @package TSwiackiewicz\EventsCollector\Event
 */
class EventController implements Controller
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
     * @param Settings $settings
     * @param Counters $counters
     * @return EventController
     */
    public static function create(Settings $settings, Counters $counters)
    {
        return new static(
            EventService::create($settings, $counters)
        );
    }

    /**
     * @param string $method
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidControllerDefinitionException
     */
    public function invoke($method, Request $request)
    {
        switch ($method)
        {
            case 'getEvents':
                return $this->getEvents();

            case 'getEvent':
                return $this->getEvent($request);

            case 'registerEvent':
                return $this->registerEvent($request);

            case 'unregisterEvent':
                return $this->unregisterEvent($request);

            case 'collectEvent':
                return $this->collectEvent($request);
        }

        throw new InvalidControllerDefinitionException('Method `' . $method . '` is not supported by ' . __CLASS__);
    }

    /**
     * @return JsonResponse
     */
    private function getEvents()
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
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function getEvent(Request $request)
    {
        try {

            $event = $this->service->getEvent($request->query->get('event'));

        } catch (NotRegisteredException $notRegistered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function registerEvent(Request $request)
    {
        try {

            $payload = RequestPayload::fromJson($request->getContent());
            $event = Event::create($payload->getValue('type'));

            $this->service->registerEvent($event);

        } catch (AlreadyRegisteredException $registered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_CONFLICT, $registered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function unregisterEvent(Request $request)
    {
        try {

            $this->service->unregisterEvent($request->request->get('event'));

        } catch (NotRegisteredException $notRegistered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function collectEvent(Request $request)
    {
        try {

            $event = $this->service->getEvent($request->request->get('event'));

            $this->service->collectEvent($event, $request->getContent());

        } catch (NotRegisteredException $notRegistered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
        }

        return new JsonResponse(
            [
                '_id' => $event->getId()
            ],
            JsonResponse::HTTP_OK
        );
    }
}