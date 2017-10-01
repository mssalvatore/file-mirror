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

    public function register($filePath)
    {
        $watchId = $this->inotifyInstance->addWatch($filePath, self::INOTIFY_OPTIONS);
        array_push($this->watchIds, $watchId);
    }

    public function registerCallback(Callable $callback)
    {
        $this->callback = $callback;
    }

    public function processEvents()
    {
        $events = $this->inotifyInstance->readEvents();

        if ($events !== false) {
            call_user_func($this->callback);
        }
    }
}
