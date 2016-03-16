<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class WatcherEmailActionHandler
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Action\Handler
 */
class WatcherEmailActionHandler implements WatcherActionHandler
{
    /**
     * @var string
     */
    private $toAddress;

    /**
     * @var string
     */
    private $subject;

    /**
     * @param string $toAddress
     * @param string $subject
     */
    public function __construct($toAddress, $subject)
    {
        $this->toAddress = $toAddress;
        $this->subject = $subject;

        $this->validateEmailToAddress();
        $this->validateSubject();
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateEmailToAddress()
    {
        if (false === filter_var($this->toAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidParameterException('Invalid recipient email address');
        }
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateSubject()
    {
        if (empty($this->subject) || !is_string($this->subject)) {
            throw new InvalidParameterException('Not empty email message subject is required');
        }
    }

    /**
     * @param string $message
     */
    public function handle($message)
    {
        print "TO: `{$this->toAddress}`, SUBJECT: `{$this->subject}`, MESSAGE: `" . $message . "`" . PHP_EOL;

        //mail($this->toAddress, $this->subject, $message);
    }
}