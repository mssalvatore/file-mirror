<?php

namespace mssalvatore\FileMirror\Monitor;

class INotifyWrapper
{
    protected $inotifyInstance;
    public function __construct($blocking = 0)
    {
        $this->inotifyInstance = inotify_init();
        stream_set_blocking($this->inotifyInstance, $blocking);
    }

    public function __destruct()
    {
        fclose($this->inotifyInstance);
    }

    public function addWatch($filePath, $mask)
    {
        return inotify_add_watch($this->inotifyInstance, $filePath, $mask);
    }

    public function removeWatch($watchId)
    {
        return inotify_rm_watch($watchId);
    }

    public function readEvents()
    {
        $events = inotify_read($this->inotifyInstance);

        if ($events === false) {
            return array();
        }

        return $events;
    }
}
