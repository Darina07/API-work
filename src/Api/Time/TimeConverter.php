<?php

namespace Supp\Api\Time;

use DateTime;
use DateTimeZone;
use GeoIp2\Database\Reader;
use League\Container\Container;
use PHPUnit\Util\Exception;

class TimeConverter {
    private $userTimezone;
    private $defaultTimezone;
    private $defaultServerAddr;
    protected ? Container $container = null;

    public function __construct(Container $container, $userIP) {
        $this->container = $container;
        $this->defaultServerAddr = "172.241.20.189";
        //$this->defaultServerAddr = $_SERVER['SERVER_ADDR'] ?? "172.241.20.189";
        $userTimezone = $this->getUserTimezone($userIP);
        $this->userTimezone = new DateTimeZone($userTimezone);
        //$this->defaultTimezone = new DateTimeZone(date_default_timezone_get());
        $this->defaultTimezone = new DateTimeZone('America/New_York');
    }

    private function getUserTimezone($userIP){
        // Path to the GeoLite2-City database
        $databasePath = __DIR__ . "/GeoLite2-City.mmdb";

        // Create a GeoIP2 reader object
        $reader = new Reader($databasePath);

        try {
            // Get the user's location data based on the IP address
            $record = $reader->city($userIP);
            // Get the time zone from the location data
            return $record->location->timeZone;
        } catch (\Exception $e) {
            return $this->defaultServerAddr;
        }
    }

    /*
     * For GET- billing => $toDefault = false (default DB timezone to user timezone)
     * For POST and PATCH -billing => $toDefault = true (user timezone to default db timezone)
     */
    public function convertToUserTimezone($datetime, $toDefault = false) {
        // Create a DateTime object with the specified timezone
        $dateTime = new DateTime($datetime, $toDefault ? $this->userTimezone : $this->defaultTimezone);

        // Set the timezone based on the conversion direction
        $timezone = $toDefault ? $this->defaultTimezone : $this->userTimezone;
        $dateTime->setTimezone($timezone);

        // Format the converted datetime
        $convertedDatetime = $dateTime->format('Y-m-d H:i:s');

        return $convertedDatetime;
    }

}
