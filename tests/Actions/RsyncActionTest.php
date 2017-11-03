<?php

namespace mssalvatore\FileMirror\Test\Actions;

use \mssalvatore\FileMirror\Actions\RsyncAction;
use \PHPUnit\Framework\TestCase;

class RsyncActionTest extends TestCase
{
    const SOURCE_PATH =  "/root/testSourcePath";
    const DESTINATION_PATH = "/root/testDestinationPath";
    protected function setUp()
    {
        $this->mockRsyncClient = $this->createMock(\AFM\Rsync\Rsync::class);
        $this->rsyncAction = new RsyncAction($this->mockRsyncClient, self::SOURCE_PATH, self::DESTINATION_PATH);
    }

    public function testExecute()
    {
        $this->mockRsyncClient
             ->expects($this->once())
             ->method("sync")
             ->with($this->equalTo(self::SOURCE_PATH), $this->equalTo(self::DESTINATION_PATH));

        $this->rsyncAction->execute(array());
    }
}
