<?php

namespace mssalvatore\FileMirror\Monitor;

class INotifyFileMonitor implements MonitorInterface
{
    const INOTIFY_OPTIONS = IN_CREATE | IN_MODIFY | IN_DELETE;
    protected $filePath;
    protected $callback;
    protected $inotifyInstance;
    protected $watchIds;

    public function __construct(INotifyWrapper $inotifyInstance)
    {
        $this->inotifyInstance = $inotifyInstance;
        $this->watchIds = array();
    }

    public function __destruct()
    {
        $this->clearWatches();
    }

    public function clearWatches()
    {
        foreach ($this->watchIds as $watchId) {
            $this->inotifyInstance->removeWatch($watchId);
        }
    }

    public function register(RegistrationRecord $record)
    {
        $watchId = $this->inotifyInstance->addWatch($record, self::INOTIFY_OPTIONS);
        array_push($this->watchIds, $watchId);
    }

    public function checkEvents()
    {
        $events = array();
        $inotifyEvents = $this->inotifyInstance->readEvents();

        foreach ($inotifyEvents as $inotifyEvent) {
            array_push($events, new Event(time(), $inotifyEvent['mask']));
        }

        return $events;
    }
}
