<?php

namespace mssalvatore\FileMirror\Monitor;

class Event
{
    public $time;
    public $data;

    public function __construct($time, $data)
    {
        $this->time = $time;
        $this->data = $data;
    }
}
