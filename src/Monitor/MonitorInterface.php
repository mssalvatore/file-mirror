<?php

namespace mssalvatore\FileMirror\Monitor;

interface MonitorInterface
{
    public function register($filePath);
    public function registerCallback(Callable $callback);
    public function processEvents();
}
