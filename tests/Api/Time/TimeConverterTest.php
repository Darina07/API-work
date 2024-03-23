<?php

namespace Tests\Api\Time;

use DateTime;
use DateTimeZone;
use League\Container\Container;
use Supp\Api\Time\TimeConverter;
use PHPUnit\Framework\TestCase;

class TimeConverterTest extends TestCase
{
    private $timeConverter;

    public function setUp(): void
    {
        $container = new Container();
        // Replace this with a valid IP address for testing
        $userIP = '130.185.207.200';
        $this->timeConverter = new TimeConverter($container, $userIP);
    }

    public function testConvertToUserTimezone()
    {
        // Set the default timezone to UTC
        date_default_timezone_set('America/New_York');

        // Create a DateTime object with the current time
        $dateString = '2023-05-15 18:11:20';
        $datetime = new DateTime($dateString);

        // Convert the DateTime to the user's timezone
        $convertedDatetime = $this->timeConverter->convertToUserTimezone($datetime->format('Y-m-d H:i:s'));

        $expectedDateTime = '2023-05-16 01:11:20';
        // Assert that the converted datetime is not empty
        $this->assertNotEmpty($convertedDatetime);
        $this->assertSame($expectedDateTime, $convertedDatetime);
    }

    public function testGetUserTimezone()
    {
        $this->assertNotEmpty($this->invokeGetUserTimezone('130.185.207.213'));
    }

    public function testGetUserTimezoneDefault()
    {
        $this->assertNotEmpty($this->invokeGetUserTimezone('192.168.0.1'));
    }

    private function invokeGetUserTimezone($testIP)
    {
        // Create a reflection object for the private method
        $reflection = new \ReflectionClass(TimeConverter::class);
        $method = $reflection->getMethod('getUserTimezone');
        $method->setAccessible(true);

        // Call the private method with the test IP address
        return $method->invoke($this->timeConverter, $testIP);
    }

}
