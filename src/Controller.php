<?php
namespace TSwiackiewicz\EventsCollector;

use TSwiackiewicz\EventsCollector\Configuration\Configuration;

/**
 * Class Controller
 * @package TSwiackiewicz\EventsCollector
 */
abstract class Controller
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
}