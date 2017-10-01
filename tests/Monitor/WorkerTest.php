<?php

namespace mssalvatore\FileMirror\Test\Monitor;

use \mssalvatore\FileMirror\Actions\ActionInterface;
use \mssalvatore\FileMirror\Monitor\Event;
use \mssalvatore\FileMirror\Monitor\MonitorInterface;
use \mssalvatore\FileMirror\Monitor\Worker;
use \PHPUnit\Framework\TestCase;

class WorkerTest extends TestCase
{
    protected function setUp()
    {
        $this->mockMonitor = $this->createMock(MonitorInterface::class);
        $this->mockAction = $this->createMock(ActionInterface::class);

        $this->worker = new Worker($this->mockMonitor, $this->mockAction);
    }

    public function testActionExecuted()
    {
        $events = array(new Event(0, 256));
        $this->mockMonitor
              ->expects($this->once())
              ->method("checkEvents")
              ->willReturn($events);

        $this->mockAction
             ->expects($this->once())
             ->method("execute")
             ->with($events);

        $this->worker->work();
    }

    public function testActionNotExecuted()
    {
        $this->mockMonitor
              ->expects($this->once())
              ->method("checkEvents")
              ->willReturn(array());

        $this->mockAction
             ->expects($this->never())
             ->method("execute");

        $this->worker->work();
    }
}
