<?php

namespace mssalvatore\FileMirror\Monitor;

class RegistrationRecord
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
