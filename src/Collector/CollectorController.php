<?php
namespace TSwiackiewicz\EventsCollector\Collector;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TSwiackiewicz\EventsCollector\Controller;

/**
 * Class CollectorController
 * @package TSwiackiewicz\EventsCollector\Collector
 */
class CollectorController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllEventCollectors(Request $request)
    {
        $collectors = [];
        $eventType = $request->query->get('event');

        $eventCollectors = $this->configuration->getEventCollectors($eventType);
        foreach ($eventCollectors as $collector) {
            $collectors[] = [
                '_id' => $collector->getId(),
                'name' => $collector->getName()
            ];
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
        $collector = $this->configuration->getEventCollector(
            $request->query->get('event'),
            $request->query->get('collector')
        );

        return new JsonResponse(
            $collector->toArray(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unregisterEventCollector(Request $request)
    {
        $this->configuration->unregisterEventCollector(
            $request->request->get('event'),
            $request->request->get('collector')
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
    public function registerEventCollector(Request $request)
    {
        $eventType = $request->request->get('event');

        $factory = new CollectorFactory();
        $collector = $factory->create(
            $eventType,
            $request->getContent()
        );

        $this->configuration->registerEventCollector($eventType, $collector);

        return new JsonResponse(
            [
                '_id' => $collector->getId()
            ],
            JsonResponse::HTTP_CREATED
        );
    }
}
