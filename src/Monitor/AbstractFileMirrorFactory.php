<?php

namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Configuration\ConfigLoaderInterface;
use \mssalvatore\FileMirror\Monitor\AbstractWorkerFactory;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;
use \mssalvatore\FileMirror\Monitor\ShutdownSignalHandler;

abstract class AbstractFileMirrorFactory extends AbstractConfigurableFactory
{
    protected $workerFactory;
    protected $configMonitor;
    protected $configLoader;
    protected $shutdownSignalHandler;

    public function injectWorkerFactory(AbstractWorkerFactory $workerFactory)
    {
        $this->workerFactory = $workerFactory;
    }

    public function injectConfigMonitor(MonitorInterface $configMonitor)
    {
        $this->configMonitor = $configMonitor;
    }

    public function injectConfigLoader(ConfigLoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
    }

    public function injectShutdownSignalHandler(ShutdownSignalHandler $shutdownSignalHandler)
    {
        $this->shutdownSignalHandler = $shutdownSignalHandler;
    }

    abstract public function buildFileMirror();
}
