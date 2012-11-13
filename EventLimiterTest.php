<?php
include __DIR__ . '/EventLimiter.php';
include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/TestClock.php';

use org\bovigo\vfs\vfsStream;

class EventLimiterTest extends PHPUnit_Framework_TestCase
{
    private static $smsToMaxim = 'send sms to +7-923-1172801';

    protected function setUp()
    {
        vfsStream::setup('eventsRegistry');
    }

    public function test_first_event_is_allowed()
    {
        $this->assertTrue(self::el()->goingTo(self::$smsToMaxim));
    }

    public function test_second_event_isnt_allowed()
    {
        $this->assertTrue(self::el()->goingTo(self::$smsToMaxim));
        $this->assertFalse(self::el()->goingTo(self::$smsToMaxim));
    }

    public function test_second_event_is_allowed_in_11_seconds()
    {
        $clock = new TestClock();
        $this->assertTrue(self::el($clock)->goingTo(self::$smsToMaxim));
        $clock->timePassed(11);
        $this->assertTrue(self::el($clock)->goingTo(self::$smsToMaxim));
    }

    private static function el($clock = null)
    {
        return new EventLimiter(1, 10, vfsStream::url('eventsRegistry'), $clock);
    }
}
