<?php
namespace TSwiackiewicz\EventsCollector\Action\Email;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use TSwiackiewicz\EventsCollector\Action\ActionTarget;
use TSwiackiewicz\EventsCollector\Action\Exception\InvalidActionParameterException;

/**
 * Class EmailActionTarget
 * @package TSwiackiewicz\EventsCollector\Action\Email
 */
class EmailActionTarget extends ActionTarget
{
    const EMAIL_ACTION = 'email';

    const TO_ADDRESS_PARAMETER = 'to';
    const SUBJECT_PARAMETER = 'subject';

    /**
     * @param array $parameters
     * @return EmailActionTarget
     */
    public static function create(array $parameters)
    {
        return new static(self::EMAIL_ACTION, new ParameterBag($parameters));
    }

    /**
     * @throws InvalidActionParameterException
     */
    protected function validateParameters()
    {
        $toAddress = $this->parameters->get(self::TO_ADDRESS_PARAMETER);
        $subject = $this->parameters->get(self::SUBJECT_PARAMETER);

        if (empty($toAddress)) {
            throw new InvalidActionParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Email action to address parameter is required');
        }

        if (empty($subject)) {
            throw new InvalidActionParameterException(JsonResponse::HTTP_BAD_REQUEST, 'Email action subject parameter is required');
        }
    }
}