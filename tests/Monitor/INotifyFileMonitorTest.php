<?php

namespace mssalvatore\FileMirror\Test\Monitor;

use \mssalvatore\FileMirror\Monitor\INotifyWrapper;
use \mssalvatore\FileMirror\Monitor\INotifyFileMonitor;
use \PHPUnit\Framework\TestCase;


class INotifyFileMonitorTest extends TestCase
{
    protected function setUp()
    {
        $this->mockINotifyWrapper = $this->createMock(INotifyWrapper::class);
        $this->INotifyFileMonitor = new INotifyFileMonitor($this->mockINotifyWrapper);
    }

    public function testAddWatch()
    {
        $this->addFileWatches();
    }

    public function testClearWatches()
    {
        $this->addFileWatches();
        $this->mockINotifyWrapper
             ->expects($this->exactly(3))
             ->method("removeWatch")
             ->withConsecutive([1], [5], [9]);

        $this->INotifyFileMonitor->clearWatches();
    }

    protected function addFileWatches()
    {
        $this->mockINotifyWrapper
              ->expects($this->exactly(3))
              ->method("addWatch")
              ->will($this->onConsecutiveCalls(1,5,9));

        $this->INotifyFileMonitor->register("f1");
        $this->INotifyFileMonitor->register("f2");
        $this->INotifyFileMonitor->register("f3");
    }

    public function testProcessEventsFoundEvents()
    {
        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("readEvents")
             ->willReturn(array('a','b','c'));

        $mockCallable = $this->getMockForAbstractClass('\mssalvatore\FileMirror\Test\Monitor\MockCallable');
        $mockCallable->expects($this->once())
                     ->method('myCallback');

        $this->INotifyFileMonitor->registerCallback([$mockCallable, "myCallback"]);
        $this->INotifyFileMonitor->processEvents();
    }

    public function testProcessEventsFoundNoEvents()
    {
        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("readEvents")
             ->willReturn(false);

        $mockCallable = $this->getMockForAbstractClass('\mssalvatore\FileMirror\Test\Monitor\MockCallable');
        $mockCallable->expects($this->never())
                     ->method('myCallback');

        $this->INotifyFileMonitor->registerCallback([$mockCallable, "myCallback"]);
        $this->INotifyFileMonitor->processEvents();
    }
}

abstract class MockCallable
{
    public abstract function myCallback();
}

