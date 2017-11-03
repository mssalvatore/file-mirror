<?php

namespace mssalvatore\FileMirror\Monitor;

interface MonitorInterface
{
    public function register(RegistrationRecord $record);
    public function checkEvents();
    public function cleanUp();
}
