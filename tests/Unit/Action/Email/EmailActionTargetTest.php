<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Action\Email;

use TSwiackiewicz\EventsCollector\Action\Email\EmailActionTarget;
use TSwiackiewicz\EventsCollector\Action\Exception\InvalidActionParameterException;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class EmailActionTargetTest
 * @package TSwiackiewicz\EventsCollector\Tests\Action\Email
 */
class EmailActionTargetTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $toAddress = 'user@domain.com';

    /**
     * @var string
     */
    private $subject = 'Test subject';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @test
     */
    public function shouldCreateValidEmailActionTarget()
    {
        $this->parameters = [
            EmailActionTarget::TO_ADDRESS_PARAMETER => $this->toAddress,
            EmailActionTarget::SUBJECT_PARAMETER => $this->subject
        ];

        $target = EmailActionTarget::create($this->parameters);

        $this->assertTarget($target);
    }

    /**
     * @param EmailActionTarget $target
     */
    private function assertTarget(EmailActionTarget $target)
    {
        $this->assertEquals(EmailActionTarget::EMAIL_ACTION, $target->getType());
        $this->assertEquals($this->parameters, $target->getParameters());
        $this->assertEquals($this->toAddress, $target->getParameter(EmailActionTarget::TO_ADDRESS_PARAMETER));
        $this->assertEquals($this->subject, $target->getParameter(EmailActionTarget::SUBJECT_PARAMETER));
    }

    /**
     * @test
     * @dataProvider getEmailActionTargetInvalidParameters
     *
     * @param array $invalidParameters
     */
    public function shouldThrowInvalidActionParameterExceptionIfEmailActionTargetIsInvalid(array $invalidParameters)
    {
        $this->setExpectedException(InvalidActionParameterException::class);

        EmailActionTarget::create($invalidParameters);
    }

    /**
     * @return array
     */
    public function getEmailActionTargetInvalidParameters()
    {
        return [
            [
                [
                    EmailActionTarget::SUBJECT_PARAMETER => $this->subject
                ]
            ],
            [
                [
                    EmailActionTarget::TO_ADDRESS_PARAMETER => $this->toAddress
                ]
            ]
        ];
    }
}
