<?php

namespace mssalvatore\FileMirror\Actions;

use \mssalvatore\FileMirror\Monitor\AbstractConfigurableFactory;

abstract class AbstractActionFactory extends AbstractConfigurableFactory
{
    abstract public function buildAction($server, $source, $destination);
}
