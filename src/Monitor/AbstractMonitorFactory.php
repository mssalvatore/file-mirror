<?php

namespace mssalvatore\FileMirror\Monitor;

abstract class AbstractMonitorFactory extends AbstractConfigurableFactory
{
    abstract public function buildMonitor(RegistrationRecord $registrationRecord);
}
