<?php

namespace mssalvatore\FileMirror\Actions;

use \mssalvatore\FileMirror\Monitor\AbstractFactory;

abstract class AbstractActionFactory extends AbstractFactory
{
    abstract public function buildAction($server, $source, $destination);
}
