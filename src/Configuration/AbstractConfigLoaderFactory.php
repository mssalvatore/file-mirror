<?php

namespace mssalvatore\FileMirror\Configuration;

abstract class AbstractConfigLoaderFactory
{
    abstract public function buildConfigLoader();
}
