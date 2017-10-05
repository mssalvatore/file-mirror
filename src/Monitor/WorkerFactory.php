<?php

namespace mssalvatore\FileMirror\Monitor;

class WorkerFactory extends AbstractWorkerFactory
{
    public function __construct(\stdClass $config)
    {
        parent::configure($config);
    }

    public function configure(\stdClass $config)
    {
        parent::configure($config);

        $this->monitorFactory->configure($this->config);
        $this->actionFactory->configure($this->config);
    }
    public function buildWorker($server, $sourcePath, $destinationPath)
    {
        return new Worker(
            $this->monitorFactory->buildMonitor(new RegistrationRecord($sourcePath)),
            $this->actionFactory->buildAction($server, $sourcePath, $destinationPath)
        );
    }
}
