<?php

namespace mssalvatore\FileMirror\Configuration;

interface ConfigLoaderInterface
{
    public function loadConfig();
    public function getConfig();
}
