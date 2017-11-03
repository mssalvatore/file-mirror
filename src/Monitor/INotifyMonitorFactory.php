<?php

namespace mssalvatore\FileMirror\Monitor;

class INotifyMonitorFactory extends AbstractMonitorFactory
{
    public function buildMonitor(RegistrationRecord $registrationRecord)
    {
        $inotifyWrapper = new INotifyWrapper();
        $inotifyMonitor = new INotifyFileMonitor($inotifyWrapper);
        $inotifyMonitor->register($registrationRecord);

        return $inotifyMonitor;
    }
}

