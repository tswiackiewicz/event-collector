<?php
namespace TSwiackiewicz\EventsCollector\Http;

/**
 * Class RequestPayload
 * @package TSwiackiewicz\EventsCollector\Http
 */
class RequestPayload
{
    const KEY_PARTS_SEPARATOR = '.';

    /**
     * @var array
     */
    private $payload;

    /**
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param string $payload
     * @return RequestPayload
     */
    public static function fromJson($payload)
    {
        return new static(
            json_decode($payload, true)
        );
    }

    /**
     * @param string $payload
     * @return bool
     */
    public static function isJsonPayload($payload)
    {
        $decodedPayload = json_decode($payload);

        return ($decodedPayload !== null && JSON_ERROR_NONE === json_last_error());
    }

    /**
     * @param string $key
     * @return array|string|int|float|bool|null
     */
    public function getValue($key)
    {
        $value = $this->payload;

        $keyParts = $this->getKeyParts($key);
        foreach ($keyParts as $keyPart) {
            if(isset($value[$keyPart])) {
                $value = $value[$keyPart];
                continue;
            }
        }

        return $value;
    }

    /**
     * @param string $key
     * @return string[]
     */
    private function getKeyParts($key)
    {
        return explode(self::KEY_PARTS_SEPARATOR, $key);
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }
}