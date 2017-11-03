<?php

namespace mssalvatore\FileMirror\Test\Monitor;

use \mssalvatore\FileMirror\Actions\AbstractActionFactory;
use \mssalvatore\FileMirror\Actions\ActionInterface;
use \mssalvatore\FileMirror\Monitor\AbstractMonitorFactory;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;
use \mssalvatore\FileMirror\Monitor\RegistrationRecord;
use \mssalvatore\FileMirror\Monitor\WorkerFactory;
use \PHPUnit\Framework\TestCase;

class WorkerFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->config = (object) array();
        $this->config1 = (object) array("param1" => "1", "param2" => 2);

        $this->mockMonitorFactory = $this->createMock(AbstractMonitorFactory::class);
        $this->mockActionFactory = $this->createMock(AbstractActionFactory::class);

        $this->workerFactory = new WorkerFactory($this->config);
        $this->workerFactory->injectMonitorFactory($this->mockMonitorFactory);
        $this->workerFactory->injectActionFactory($this->mockActionFactory);
    }

    public function testConfigure()
    {
        $this->mockMonitorFactory
             ->expects($this->once())
             ->method("configure")
             ->with($this->config1);
        $this->mockActionFactory
             ->expects($this->once())
             ->method("configure")
             ->with($this->config1);

        $this->workerFactory->configure($this->config1);
    }

    public function testBuildWorker()
    {
        $server = "galactica";
        $sourcePath = "/my/file";
        $destinationPath = "/my/remote/location";
        $registrationRecord = new RegistrationRecord($sourcePath);
        $this->mockMonitorFactory
             ->expects($this->once())
             ->method("buildMonitor")
             ->with($registrationRecord)
             ->willReturn($this->createMock(MonitorInterface::class));
        $this->mockActionFactory
             ->expects($this->once())
             ->method("buildAction")
             ->with($server, $sourcePath, $destinationPath)
             ->willReturn($this->createMock(ActionInterface::class));

        //$this->mockMonitor = $this->createMock(MonitorInterface::class);
        //$this->mockAction = $this->createMock(ActionInterface::class);
        $this->workerFactory->buildWorker($server, $sourcePath, $destinationPath);
    }
}

