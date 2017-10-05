<?php

namespace mssalvatore\FileMirror\Monitor;

abstract class AbstractMonitorFactory extends AbstractFactory
{
    abstract public function buildMonitor(RegistrationRecord $registrationRecord);
}
