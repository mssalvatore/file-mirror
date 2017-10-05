<?php

namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Actions\AbstractActionFactory;

abstract class AbstractWorkerFactory extends AbstractFactory
{
    protected $monitorFactory;
    protected $actionFactory;

    public function injectMonitorFactory(AbstractMonitorFactory $monitorFactory)
    {
        $this->monitorFactory = $monitorFactory;
    }

    public function injectActionFactory(AbstractActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    abstract public function buildWorker($server, $sourcePath, $destinationPath);
}
