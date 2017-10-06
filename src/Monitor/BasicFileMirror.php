<?php

namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Configuration\ConfigLoaderInterface;
use \mssalvatore\FileMirror\Monitor\AbstractWorkerFactory;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;

class BasicFileMirror extends AbstractFileMirror
{
    protected $shouldRun;

    public function __construct(AbstractWorkerFactory $workerFactory, MonitorInterface $configMonitor, ConfigLoaderInterface $configLoader)
    {
        parent::__construct($workerFactory, $configMonitor, $configLoader);

        $this->buildWorkers();

        $this->shouldRun = true;
    }

    public function run()
    {
        while ($this->shouldRun) {
            if (count($this->configMonitor->checkEvents()) > 0) {
                $this->removeAllWorkers();
                $this->loadConfig();
                $this->buildWorkers();
            }

            foreach ($this->workers as $worker) {
                $worker->work();
            }
            sleep(2);
        }
    }

    protected function buildWorkers()
    {
        foreach ($this->config->mirrorFiles as $mirrorFiles) {
            foreach ($mirrorFiles->serverNames as $serverName) {
                foreach ($mirrorFiles->files as $sourceFile) {
                    $this->addWorker($this->workerFactory->buildWorker($serverName, $sourceFile, $mirrorFiles->destinationDirectory));
                }
            }
        }
    }

    public function shutdown()
    {
        echo "I'VE BEEN SHUT DOWN\n";
        $this->shouldRun = false;
    }
}
