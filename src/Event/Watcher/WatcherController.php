<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Controller;
use TSwiackiewicz\EventsCollector\Counters\Counters;
use TSwiackiewicz\EventsCollector\Exception\AlreadyRegisteredException;
use TSwiackiewicz\EventsCollector\Exception\InvalidControllerDefinitionException;
use TSwiackiewicz\EventsCollector\Exception\NotRegisteredException;
use TSwiackiewicz\EventsCollector\Http\JsonErrorResponse;
use TSwiackiewicz\EventsCollector\Settings\Settings;

/**
 * Class WatcherController
 * @package TSwiackiewicz\EventsCollector\Event\Watcher
 */
class WatcherController implements Controller
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
     * @param Settings $settings
     * @param Counters $counters
     * @return WatcherController
     */
    public static function create(Settings $settings, Counters $counters)
    {
        $service = WatcherService::create($settings, $counters);
        $factory = new WatcherFactory();

        return new static($service, $factory);
    }

    /**
     * @param string $method
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidControllerDefinitionException
     */
    public function invoke($method, Request $request)
    {
        switch ($method) {
            case 'getEventWatchers':
                return $this->getEventWatchers($request);

            case 'getEventWatcher':
                return $this->getEventWatcher($request);

            case 'registerEventWatcher':
                return $this->registerEventWatcher($request);

            case 'unregisterEventWatcher':
                return $this->unregisterEventWatcher($request);
        }

        throw new InvalidControllerDefinitionException('Method `' . $method . '` is not supported by ' . __CLASS__);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function getEventWatchers(Request $request)
    {
        try {

            $eventType = $request->query->get('event');
            $eventWatchers = $this->service->getEventWatchers($eventType);

            $watchers = [];
            foreach ($eventWatchers as $eventWatcher) {
                $watchers[] = [
                    '_id' => $eventWatcher->getId(),
                    'name' => $eventWatcher->getName()
                ];
            }

        } catch (NotRegisteredException $notRegistered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function getEventWatcher(Request $request)
    {
        try {

            $watcher = $this->service->getEventWatcher(
                $request->query->get('event'),
                $request->query->get('watcher')
            );

        } catch (NotRegisteredException $notRegistered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function registerEventWatcher(Request $request)
    {
        try {

            $watcher = $this->factory->create(
                $request->request->get('event'),
                $request->getContent()
            );

            $this->service->registerEventWatcher($watcher);

        } catch (AlreadyRegisteredException $registered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_CONFLICT, $registered->getMessage());
        } catch (NotRegisteredException $notRegistered) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_NOT_FOUND, $notRegistered->getMessage());
        } catch (\Exception $e) {
            return JsonErrorResponse::createJsonResponse(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
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
    private function unregisterEventWatcher(Request $request)
    {
        try {

            $this->service->unregisterEventWatcher(
                $request->request->get('event'),
                $request->request->get('watcher')
            );

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
}