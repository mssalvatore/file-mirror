<?php

namespace mssalvatore\FileMirror\Monitor;

abstract class AbstractConfigurableFactory
{
    protected $config;

    public function __construct(\stdClass $config)
    {
        $this->configure($config);
    }

    public function configure(\stdClass $config)
    {
        $this->config = $config;
    }
}
