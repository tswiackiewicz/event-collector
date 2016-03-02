<?php
namespace TSwiackiewicz\EventsCollector\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use TSwiackiewicz\EventsCollector\Action\Email\EmailActionTarget;
use TSwiackiewicz\EventsCollector\Action\Exception\InvalidActionParameterException;
use TSwiackiewicz\EventsCollector\Action\Exception\UnknownActionTypeException;
use TSwiackiewicz\EventsCollector\Http\RequestPayload;

/**
 * Class ActionFactory
 * @package TSwiackiewicz\EventsCollector\Action
 */
class ActionFactory
{
    /**
     * @param string $event
     * @param string $jsonPayload
     * @return Action
     * @throws InvalidActionParameterException
     * @throws UnknownActionTypeException
     */
    public function create($event, $jsonPayload)
    {
        $payload = RequestPayload::fromJson($jsonPayload);
        $type = $payload->getValue('target.type');

        if (empty($type)) {
            throw new InvalidActionParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Action type is required');
        }

        switch ($type) {
            case EmailActionTarget::EMAIL_ACTION:
                return Action::create(
                    $payload->getValue('name'),
                    $event,
                    $payload->getValue('threshold'),
                    $payload->getValue('aggregation_key'),
                    EmailActionTarget::create(
                        $payload->getValue('target.parameters')
                    )
                );
        }

        throw new UnknownActionTypeException(JsonResponse::HTTP_BAD_REQUEST, 'Unknown action type: `' . $type . '`');
    }
}