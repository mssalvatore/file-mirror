<?php

namespace mssalvatore\FileMirror\Test\Monitor;

use \mssalvatore\FileMirror\Monitor\INotifyWrapper;
use \mssalvatore\FileMirror\Monitor\INotifyFileMonitor;
use \mssalvatore\FileMirror\Monitor\RegistrationRecord;
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

        $this->INotifyFileMonitor->register(new RegistrationRecord("f1"));
        $this->INotifyFileMonitor->register(new RegistrationRecord("f2"));
        $this->INotifyFileMonitor->register(new RegistrationRecord("f3"));
    }

    public function testProcessEventsFoundEvents()
    {
        $event0 = array('wd' => 1, 'mask' => 256, "cookie" => 0, "name" => "f1");
        $event1 = array('wd' => 5, 'mask' => 512, "cookie" => 0, "name" => "f2");
        $event2 = array('wd' => 9, 'mask' => 128, "cookie" => 0, "name" => "f3");
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
}
