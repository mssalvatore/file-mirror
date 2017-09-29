<?php

namespace mssalvatore\FileMirror\Monitor;

interface FileMonitorInterface
{
    public function registerFile($filePath);
    public function registerCallback(Callable $callback);
    public function processEvents();
}
