<?php

namespace mssalvatore\FileMirror\Actions;

interface ActionInterface
{
    public function execute(array $events);
}
