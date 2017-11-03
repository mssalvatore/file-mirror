<?php

namespace mssalvatore\FileMirror\Test\Monitor;

use \mssalvatore\FileMirror\Monitor\INotifyWrapper;
use \mssalvatore\FileMirror\Monitor\INotifyFileMonitor;
use \mssalvatore\FileMirror\Monitor\RegistrationRecord;
use \PHPUnit\Framework\TestCase;
use \org\bovigo\vfs\vfsStream;
use \org\bovigo\vfs\vfsStreamDirectory;
use \org\bovigo\vfs\vfsStreamWrapper;


class INotifyFileMonitorTest extends TestCase
{
    protected function setUp()
    {
        $this->mockINotifyWrapper = $this->createMock(INotifyWrapper::class);
        $this->INotifyFileMonitor = new INotifyFileMonitor($this->mockINotifyWrapper);

        vfsStreamWrapper::register();
        $vfsStreamDir = new vfsStreamDirectory('monitor');
        vfsStreamWrapper::setRoot($vfsStreamDir);

        $this->fileUrl = vfsStream::url('monitor/myDir');
    }

    public function testRegister()
    {
        $this->register();
    }

    public function testCleanUp()
    {
        $this->register();
        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("removeWatch")
             ->with(5);

        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("close");

        $this->INotifyFileMonitor->cleanUp();
    }

    protected function register()
    {
        $this->mockINotifyWrapper
              ->expects($this->once())
              ->method("addWatch")
              ->willReturn(5);

        $this->INotifyFileMonitor->register(new RegistrationRecord("f1"));
    }

    public function testProcessEventsFoundEvents()
    {
        $event0 = array('wd' => 1, 'mask' => 256, "cookie" => 0, "name" => "f1");
        $event1 = array('wd' => 1, 'mask' => 512, "cookie" => 0, "name" => "f1");
        $event2 = array('wd' => 1, 'mask' => 128, "cookie" => 0, "name" => "f1");
        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("readEvents")
             ->willReturn(array($event0, $event1, $event2));

        $events = $this->INotifyFileMonitor->checkEvents();
        $this->assertEquals($events[0]->data, $event0['mask']);
        $this->assertEquals($events[1]->data, $event1['mask']);
        $this->assertEquals($events[2]->data, $event2['mask']);
    }

    public function testProcessEventsFoundNoEvents()
    {
        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("readEvents")
             ->willReturn(array());

        $events = $this->INotifyFileMonitor->checkEvents();
        $this->assertEquals(count($events), 0);
    }

    public function testHandleIgnoredEvent()
    {
        $event = array('wd' => 5, 'mask' => IN_IGNORED, "cookie" => 0, "name" => "f1");

        $this->mockINotifyWrapper
              ->expects($this->once())
              ->method("addWatch")
              ->willReturn(5);

        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("readEvents")
             ->willReturn(array($event));

        $this->mockINotifyWrapper
             ->expects($this->never())
             ->method("removeWatch");

        $this->INotifyFileMonitor->register(new RegistrationRecord($this->fileUrl));

        $this->INotifyFileMonitor->checkEvents();
        $this->INotifyFileMonitor->cleanUp();
    }

    public function testReregister()
    {
        $event = array('wd' => 5, 'mask' => IN_IGNORED, "cookie" => 0, "name" => "f1");

        $this->mockINotifyWrapper
              ->expects($this->exactly(2))
              ->method("addWatch")
              ->will($this->onConsecutiveCalls(5,7));

        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("readEvents")
             ->willReturn(array($event));

        $this->mockINotifyWrapper
             ->expects($this->once())
             ->method("removeWatch");

        $this->INotifyFileMonitor->register(new RegistrationRecord(vfsStream::url('monitor')));

        $this->INotifyFileMonitor->checkEvents();
        $this->INotifyFileMonitor->cleanUp();
    }
}
