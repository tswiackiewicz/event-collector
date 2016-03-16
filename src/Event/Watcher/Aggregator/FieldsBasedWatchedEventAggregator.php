<?php
namespace TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator;

use TSwiackiewicz\EventsCollector\Exception\InvalidParameterException;

/**
 * Class FieldsBasedWatchedEventAggregator
 * @package TSwiackiewicz\EventsCollector\Event\Watcher\Aggregator
 */
class FieldsBasedWatchedEventAggregator implements WatchedEventAggregator
{
    const KEY_PARTS_SEPARATOR = '__';

    /**
     * @var string[]
     */
    private $fields = [];

    /**
     * @param string[] $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;

        $this->validateFields();
    }

    /**
     * @throws InvalidParameterException
     */
    private function validateFields()
    {
        if (empty($this->fields)) {
            throw new InvalidParameterException('Not empty aggregator.fields list expected for fields based aggregator');
        }
    }

    /**
     * @param string $eventType
     * @return string
     */
    public function buildAggregationKey($eventType)
    {
        return implode(
            self::KEY_PARTS_SEPARATOR,
            array_merge(
                [
                    $eventType
                ],
                $this->fields
            )
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::FIELDS_AGGREGATOR,
            'fields' => $this->fields
        ];
    }
}