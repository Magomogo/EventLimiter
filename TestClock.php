<?php

class TestClock
{
    private $time;

    public function __construct()
    {
        $this->time = strtotime('2012-12-21 10:00');
    }

    public function timePassed($seconds)
    {
        $this->time += $seconds;
    }

    public function now()
    {
        return $this->time;
    }
}
