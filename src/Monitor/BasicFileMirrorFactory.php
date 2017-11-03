<?php

namespace mssalvatore\FileMirror\Monitor;


class BasicFileMirrorFactory extends AbstractFileMirrorFactory
{
    public function buildFileMirror()
    {
        $this->shutdownSignalHandler->registerSignal(SIGTERM);
        $this->shutdownSignalHandler->registerSignal(SIGINT);
        return new BasicFileMirror($this->workerFactory, $this->configMonitor, $this->configLoader, $this->shutdownSignalHandler);
    }
}
