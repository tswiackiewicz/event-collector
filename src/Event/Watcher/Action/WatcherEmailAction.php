<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Action;

use Symfony\Component\HttpFoundation\ParameterBag;
use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class WatcherEmailAction
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Action
 */
class WatcherEmailAction extends WatcherAction
{
    const TO_ADDRESS_PARAMETER = 'to';
    const SUBJECT_PARAMETER = 'subject';

    /**
     * @param array $parameters
     * @return WatcherEmailAction
     */
    public static function create(array $parameters)
    {
        return new static(self::EMAIL_ACTION, new ParameterBag($parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'to' => $this->parameters->get(self::TO_ADDRESS_PARAMETER),
            'subject' => $this->parameters->get(self::SUBJECT_PARAMETER)
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validateParameters()
    {
        $toAddress = $this->parameters->get(self::TO_ADDRESS_PARAMETER);
        $subject = $this->parameters->get(self::SUBJECT_PARAMETER);

        if (empty($toAddress)) {
            throw new InvalidParameterException('Not empty email action.to address is required');
        }

        if (empty($subject)) {
            throw new InvalidParameterException('Not empty email action.subject is required');
        }
    }
}