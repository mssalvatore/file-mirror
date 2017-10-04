<?php

namespace mssalvatore\FileMirror\Actions;

abstract class AbstractActionFactory
{
    protected $config;

    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }

    abstract public function buildAction($server, $source, $destination);
}
