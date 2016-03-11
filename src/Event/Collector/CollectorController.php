<?php
namespace TSwiackiewicz\EventsCollector\Event\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Http\JsonException;

/**
 * Class CollectorController
 * @package TSwiackiewicz\EventsCollector\Event\Collector
 */
class CollectorController
{
    /**
     * @var CollectorService
     */
    private $service;

    /**
     * @var CollectorFactory
     */
    private $factory;

    /**
     * @param CollectorService $service
     * @param CollectorFactory $factory
     */
    public function __construct(CollectorService $service, CollectorFactory $factory)
    {
        $this->service = $service;
        $this->factory = $factory;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventCollectors(Request $request)
    {
        try {

            $eventType = $request->query->get('event');
            $eventCollectors = $this->service->getEventCollectors($eventType);

            $collectors = [];
            foreach ($eventCollectors as $eventCollector) {
                $collectors[] = [
                    '_id' => $eventCollector->getId(),
                    'name' => $eventCollector->getName()
                ];
            }

        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            $collectors,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventCollector(Request $request)
    {
        try {

            $collector = $this->service->getEventCollector(
                $request->query->get('event'),
                $request->query->get('collector')
            );

        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            $collector->toArray(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerEventCollector(Request $request)
    {
        try {

            $collector = $this->factory->create(
                $request->request->get('event'),
                $request->getContent()
            );

            $this->service->registerEventCollector($collector);

        } catch (AlreadyRegisteredException $registered) {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $registered->getMessage()))->getJsonResponse();
        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            [
                '_id' => $collector->getId()
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterEventCollector(Request $request)
    {
        try {

            $this->service->unregisterEventCollector(
                $request->request->get('event'),
                $request->request->get('collector')
            );

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
}
