<?php

namespace mssalvatore\FileMirror\Monitor;

use mssalvatore\FileMirror\Exceptions\INotifyException;

class INotifyWrapper
{
    protected $inotifyInstance;
    public function __construct($blocking = 0)
    {
        $this->initialize($blocking);
    }

    public function initialize($blocking = 0)
    {
        $this->inotifyInstance = inotify_init();
        if ($this->inotifyInstance === false) {
            throw new INotifyException("Failed to initialize inotify instance -- " . error_get_last()['message']);
        }
        stream_set_blocking($this->inotifyInstance, $blocking);
    }

    public function close()
    {
        $closed = fclose($this->inotifyInstance);
        if ($closed === false) {
            throw new INotifyException("Failed to close inotify instance -- " . error_get_last()['message']);
        }
    }

    public function addWatch($filePath, $mask)
    {
        $watchId = inotify_add_watch($this->inotifyInstance, $filePath, $mask);
        if ($watchId === FALSE) {
            throw new INotifyException("Failed to add inotify watch for file \"$filePath\" -- " . error_get_last()['message']);
        }

        return $watchId;
    }

    public function removeWatch($watchId)
    {
        $watchRemoved = inotify_rm_watch($this->inotifyInstance, $watchId);
        if ($watchRemoved === false) {
            throw new INotifyException("Failed to remove inotify watch for watchId \"$watchId\" -- " . error_get_last()['message']);
        }
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
