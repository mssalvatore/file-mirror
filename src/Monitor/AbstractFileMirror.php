<?php

namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Configuration\ConfigLoaderInterface;
use \mssalvatore\FileMirror\Monitor\AbstractWorkerFactory;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;


abstract class AbstractFileMirror
{
    protected $workerFactory;
    protected $configMonitor;
    protected $configLoader;
    protected $config;
    protected $workers;

    public function __construct(AbstractWorkerFactory $workerFactory, MonitorInterface $configMonitor, ConfigLoaderInterface $configLoader)
    {
        $this->workerFactory = $workerFactory;
        $this->configMonitor = $configMonitor;
        $this->configLoader = $configLoader;

        $this->loadConfig();

        $this->workers = array();
    }

    public function addWorker(Worker $worker)
    {
        $this->workers[] = $worker;
    }

    public function removeAllWorkers()
    {
        foreach ($this->workers as $worker) {
            $worker->cleanUp();
        }
        $this->workers = array();
    }

    protected function loadConfig()
    {
        $this->config = $this->configLoader->loadConfig();
        $this->workerFactory->configure($this->config);
    }

    abstract public function run();
}
