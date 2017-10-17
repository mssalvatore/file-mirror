<?php

namespace mssalvatore\FileMirror\Monitor;

use \mssalvatore\FileMirror\Exceptions\SignalHandlerException;

class ShutdownSignalHandler
{
    protected $supportedSignals;
    protected $shutdownCallback;

    public function __construct()
    {
        $this->shutdownCallback = null;
        $this->supportedSignals = array();
    }

    public function registerShutdownCallback(Callable $shutdownCallback)
    {
        $this->shutdownCallback = $shutdownCallback;
    }

    public function registerSignal($signal)
    {
        pcntl_signal($signal, array(&$this, "handleSignal"));
        $this->supportedSignals[$signal] = $signal;
    }

    public function handleSignal($signal)
    {
        if (!array_key_exists($signal, $this->supportedSignals)) {
            throw new SignalHandlerException("Could not handle unknown signal " . $signal);
        }

        if (is_null($this->shutdownCallback)) {
            throw new SignalHandlerException("Could not call signal handler callback: No signal handler is set");
        }

        ($this->shutdownCallback)();
    }

    public function handleQueuedSignals()
    {
        pcntl_signal_dispatch();
    }
}
