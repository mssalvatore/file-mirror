<?php

namespace mssalvatore\FileMirror\Monitor;

class INotifyFileMonitor extends \Thread implements FileMonitorInterface
{
    protected $filePath;
    protected $callback;

    public function registerFile($filePath)
    {
    }
    public function registerCallaback(Callable $callback)
    {
    }
    public function processEvents()
    {
    }
}
