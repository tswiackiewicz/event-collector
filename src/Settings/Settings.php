<?php
namespace TSwiackiewicz\EventsCollector\Settings;

use TSwiackiewicz\EventsCollector\Settings\Event\Collector\CollectorSettings;
use TSwiackiewicz\EventsCollector\Settings\Event\EventSettings;
use TSwiackiewicz\EventsCollector\Settings\Event\Watcher\WatcherSettings;

/**
 * Interface Settings
 * @package TSwiackiewicz\EventsCollector\Settings
 */
interface Settings extends EventSettings, CollectorSettings, WatcherSettings
{
    /**
     * @param string $file
     * @return Settings
     */
    public static function loadFromFile($file = '');

    /**
     * @param string $file
     */
    public function dump($file);
}