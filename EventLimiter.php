<?php

class EventLimiter
{
    private $limit;

    private $timeSpan;

    private $registryPath;

    private $testClock;

    public function __construct($limit, $timeSpan, $registryPath, $testClock = null)
    {
        $this->limit = $limit;
        $this->timeSpan = $timeSpan;
        $this->registryPath = rtrim($registryPath, '/') . '/';
        $this->testClock = $testClock;
    }

    public function goingTo($eventFingerprint)
    {
        $eventsCount = $this->lastEventIsTooClose($eventFingerprint) ?
            $this->eventsDone($eventFingerprint) : 0;

        if ($eventsCount < $this->limit) {
            $this->registerEvent($eventFingerprint);
            return true;
        }
        return false;
    }

    private function lastEventIsTooClose($eventFingerprint)
    {
        return $this->lastEventTime($eventFingerprint) + $this->timeSpan
            >= $this->currentTime();
    }

    private function eventsDone($fingerprint)
    {
        $eventRegistryFile = $this->eventRegistryFile($fingerprint);
        return is_file($eventRegistryFile) ? intval(file_get_contents($eventRegistryFile)) : 0;
    }

    private function registerEvent($fingerprint)
    {
        file_put_contents(
            $this->eventRegistryFile($fingerprint),
            $this->eventsDone($fingerprint) + 1
        );
        touch($this->eventRegistryFile($fingerprint), $this->currentTime());
    }

    private function lastEventTime($fingerprint)
    {
        return is_file($this->eventRegistryFile($fingerprint)) ?
            filemtime($this->eventRegistryFile($fingerprint)) : null;
    }


    private function eventRegistryFile($fingerprint)
    {
        return $this->registryPath . escapeshellarg($fingerprint);
    }

    private function currentTime()
    {
        return $this->testClock ? $this->testClock->now() : time();
    }
}
