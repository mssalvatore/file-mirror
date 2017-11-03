<?php

namespace mssalvatore\FileMirror\Actions;

class RsyncAction implements ActionInterface
{
    protected $rsyncClient;
    protected $sourcePath;
    protected $destinationPath;
    public function __construct(\AFM\Rsync\Rsync $rsyncClient, $sourcePath, $destinationPath)
    {
        $this->rsyncClient = $rsyncClient;
        $this->sourcePath = $sourcePath;
        $this->destinationPath = $destinationPath;
    }

    public function execute(array $events)
    {
        $this->rsyncClient->sync($this->sourcePath, $this->destinationPath);
    }

    public function cleanup()
    {
    }

    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    public function getDestinationPath()
    {
        return $this->destinationPath;
    }
}
