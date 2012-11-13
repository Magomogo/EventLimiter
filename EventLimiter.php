<?php

class EventLimiter
{
    private $limit;

    private $timeSpan;

    private $registryPath;

    public function __construct($limit, $timeSpan, $registryPath)
    {
        $this->limit = $limit;
        $this->timeSpan = $timeSpan;
        $this->registryPath = rtrim($registryPath, '/') . '/';
    }

    public function goingTo($eventFingerprint)
    {
        if ($this->eventsDone($eventFingerprint) < $this->limit) {
            $this->registerEvent($eventFingerprint);
            return true;
        }
        return false;
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
    }

    private function eventRegistryFile($fingerprint)
    {
        return $this->registryPath . escapeshellarg($fingerprint);
    }
}
