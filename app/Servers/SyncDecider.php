<?php
/**
 * by stephan scheide
 */

namespace App\Servers;


class SyncDecider
{

    const CLIENT_INCOMING_THRESHOLD = 100;

    const SECONDS_NOT_SYNCED_THRESHOLD = 300; //5 min

    private $forced = false;

    private $countClientIncoming = 0;

    private $lastSyncTime = 0;

    public function __construct()
    {
        $this->forced = false;
    }

    public function forceSyncOnNextCheck()
    {
        $this->forced = true;
    }

    public function needsSync()
    {
        if ($this->forced) {
            $this->forced = false;
            $this->updateLastSyncTimeToNow();
            return true;
        }

        if ($this->countClientIncoming > self::CLIENT_INCOMING_THRESHOLD) {
            $this->updateLastSyncTimeToNow();
            $this->countClientIncoming = 0;
            return true;
        }

        $currentTime = time();
        $delta = $currentTime - $this->lastSyncTime;
        if ($delta > self::SECONDS_NOT_SYNCED_THRESHOLD) {
            $this->updateLastSyncTimeToNow();
            return true;
        }

        return false;
    }

    public function onClientIncoming()
    {
        $this->countClientIncoming++;
    }

    private function updateLastSyncTimeToNow()
    {
        $this->lastSyncTime = time();
    }

}
