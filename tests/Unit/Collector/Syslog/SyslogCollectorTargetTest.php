<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit\Collector\Syslog;

use TSwiackiewicz\EventsCollector\Collector\Exception\InvalidCollectorParameterException;
use TSwiackiewicz\EventsCollector\Collector\Syslog\SyslogCollectorTarget;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;

/**
 * Class SyslogCollectorTargetTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit\Collector\Syslog
 */
class SyslogCollectorTargetTest extends BaseTestCase
{
    /**
     * @var string
     */
    private $ident = 'test';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @test
     */
    public function shouldCreateValidSyslogCollectorTarget()
    {
        $this->parameters = [
            SyslogCollectorTarget::IDENT_PARAMETER => $this->ident
        ];

        $target = SyslogCollectorTarget::create($this->parameters);

        $this->assertTarget($target);
    }


    /**
     * @param SyslogCollectorTarget $target
     */
    private function assertTarget(SyslogCollectorTarget $target)
    {
        $this->assertEquals(SyslogCollectorTarget::SYSLOG_COLLECTOR, $target->getType());
        $this->assertEquals($this->parameters, $target->getParameters());
        $this->assertEquals($this->ident, $target->getParameter(SyslogCollectorTarget::IDENT_PARAMETER));
    }

    /**
     * @test
     */
    public function shouldThrowInvalidCollectorParameterExceptionIfSyslogIdentIsInvalid()
    {
        $this->setExpectedException(InvalidCollectorParameterException::class);

        SyslogCollectorTarget::create([]);
    }
}
