<?php
namespace TSwiackiewicz\EventsCollector\Tests\Unit;

use Ramsey\Uuid\Uuid as RamseyUuid;
use TSwiackiewicz\EventsCollector\Tests\BaseTestCase;
use TSwiackiewicz\EventsCollector\Uuid;

/**
 * Class UuidTest
 * @package TSwiackiewicz\EventsCollector\Tests\Unit
 */
class UuidTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldGenerateUuid()
    {
        $uuid = Uuid::generate();

        $this->assertTrue(RamseyUuid::isValid($uuid->getUuid()));
    }
}
