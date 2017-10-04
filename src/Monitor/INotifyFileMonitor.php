<?php

namespace mssalvatore\FileMirror\Monitor;

class INotifyFileMonitor implements MonitorInterface
{
    const INOTIFY_OPTIONS = IN_CREATE | IN_MODIFY | IN_DELETE | IN_DELETE_SELF;
    protected $filePath;
    protected $callback;
    protected $inotifyInstance;
    protected $watchId;
    protected $registrationRecord;

    public function __construct(INotifyWrapper $inotifyInstance)
    {
        $this->inotifyInstance = $inotifyInstance;
        $this->watchId = -1;
        $this->registrationRecord = new RegistrationRecord("");
    }

    public function cleanUp()
    {
        $this->cleanUpWatch();
        $this->inotifyInstance->close();
    }

    protected function cleanUpWatch()
    {
        if ($this->watchId >= 0) {
            $this->inotifyInstance->removeWatch($this->watchId);
        }

        $this->watchId = -1;
    }

    public function register(RegistrationRecord $record)
    {
        $this->cleanUpWatch();
        $this->registrationRecord = $record;
        $watchId = $this->inotifyInstance->addWatch($record->data, self::INOTIFY_OPTIONS);
        $this->watchId = $watchId;
    }

    public function checkEvents()
    {
        $events = array();
        $inotifyEvents = $this->inotifyInstance->readEvents();

        foreach ($inotifyEvents as $inotifyEvent) {
            array_push($events, new Event(time(), $inotifyEvent['mask']));
        }

        $this->handledIgnoredEvent($inotifyEvents);
        $this->reregister();

        return $events;
    }

    protected function handledIgnoredEvent(array $inotifyEvents)
    {
        foreach ($inotifyEvents as $inotifyEvent) {
            if ($inotifyEvent['mask'] == IN_IGNORED) {
                $this->watchId = -1;
                break;
            }
        }
    }

    protected function reregister()
    {
        if ($this->watchId < 0 && file_exists($this->registrationRecord->data))
        {
            $this->register($this->registrationRecord);
        }
    }
}
