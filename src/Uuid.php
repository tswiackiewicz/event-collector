<?php
namespace TSwiackiewicz\EventsCollector;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class Uuid
 * @package TSwiackiewicz\Events
 */
class Uuid
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @param string $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Uuid
     */
    public static function generate()
    {
        return new static(
            RamseyUuid::uuid4()->toString()
        );
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}