<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Http\JsonException;

/**
 * Class WatcherController
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class WatcherController
{
    /**
     * @var WatcherService
     */
    private $service;

    /**
     * @var WatcherFactory
     */
    private $factory;

    /**
     * @param WatcherService $service
     * @param WatcherFactory $factory
     */
    public function __construct(WatcherService $service, WatcherFactory $factory)
    {
        $this->service = $service;
        $this->factory = $factory;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventWatchers(Request $request)
    {
        try {

            $eventWatchers = $this->service->getEventWatchers(
                $request->query->get('event')
            );

            $watchers = [];
            foreach ($eventWatchers as $eventWatcher) {
                $watchers[] = [
                    '_id' => $eventWatcher->getId(),
                    'name' => $eventWatcher->getName()
                ];
            }

        } catch (AlreadyRegisteredException $registered) {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $registered->getMessage()))->getJsonResponse();
        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            $watchers,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventWatcher(Request $request)
    {
        try {

            $watcher = $this->service->getEventWatcher(
                $request->query->get('event'),
                $request->query->get('watcher')
            );

        } catch (AlreadyRegisteredException $registered) {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $registered->getMessage()))->getJsonResponse();
        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            $watcher->toArray(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerEventWatcher(Request $request)
    {
        try {

            $watcher = $this->factory->create(
                $request->request->get('event'),
                $request->getContent()
            );

            $this->service->registerEventWatcher($watcher);

        } catch (AlreadyRegisteredException $registered) {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $registered->getMessage()))->getJsonResponse();
        } catch (NotRegisteredException $notRegistered) {
            return (new JsonException(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage()))->getJsonResponse();
        } catch (\Exception $e) {
            return (new JsonException(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage()))->getJsonResponse();
        }

        return new JsonResponse(
            [
                '_id' => $watcher->getId()
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterEventWatcher(Request $request)
    {
        try {

            $this->service->unregisterEventWatcher(
                $request->request->get('event'),
                $request->request->get('watcher')
            );

        } catch (AlreadyRegisteredException $registered) {
            return (new JsonException(JsonResponse::HTTP_CONFLICT, $registered->getMessage()))->getJsonResponse();
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