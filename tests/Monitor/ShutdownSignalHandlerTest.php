<?php

// Must be in this namespace so pcntl_* calls can be overriden
namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Monitor\ShutdownSignalHandler;
use \PHPUnit\Framework\TestCase;

function pcntl_signal($signal, $caller)
{
}

function pcntl_signal_dispatch()
{
}



class ShutdownSignalHandlerTest extends TestCase
{

    protected $callbackWasCalled;
    protected function setUp()
    {
        $this->callbackWasCalled = false;
        $this->handler = new ShutdownSignalhandler();
    }

    public function myCallback()
    {
        $this->callbackWasCalled = true;
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\SignalHandlerException
     * @expectedExceptionMessage Could not handle unknown signal 2
     */
    public function testHandleSignalThrowsException()
    {
        $this->handler->handleSignal(2);
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\SignalHandlerException
     * @expectedExceptionMessage Could not call signal handler callback: No signal handler is set
     */
    public function testSignalHandlerCallsCallback()
    {
        $this->handler->registerSignal(2);
        $this->handler->handleSignal(2);
    }

    public function testCallbackCalled()
    {
        $this->handler->registerShutdownCallback(array(&$this, "myCallback"));
        $this->handler->registerSignal(2);
        $this->handler->handleSignal(2);

        $this->assertTrue($this->callbackWasCalled);
    }
}
