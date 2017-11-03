<?php

namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Actions\ActionInterface;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;

class Worker
{
    protected $monitor;
    protected $action;

    public function __construct(MonitorInterface $monitor, ActionInterface $action)
    {
        $this->monitor = $monitor;
        $this->action = $action;
    }

    public function work()
    {
        $events = $this->monitor->checkEvents();

        if (count($events) > 0) {
            $this->action->execute($events);
        }
    }

    public function cleanUp()
    {
        $this->monitor->cleanUp();
        $this->action->cleanUp();
    }
}
