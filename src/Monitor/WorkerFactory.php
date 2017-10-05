<?php

namespace mssalvatore\FileMirror\Monitor;

class WorkerFactory extends AbstractWorkerFactory
{
    public function buildWorker($server, $sourcePath, $destinationPath)
    {
        return new Worker(
            $this->monitorFactory->buildMonitor(new RegistrationRecord($sourcePath)),
            $this->actionFactory->buildAction($server, $sourcePath, $destinationPath)
        );
    }
}
