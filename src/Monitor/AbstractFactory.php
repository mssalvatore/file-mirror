<?php

namespace mssalvatore\FileMirror\Monitor;

abstract class AbstractFactory
{
    protected $config;

    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }
}
