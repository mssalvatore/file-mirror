<?php

namespace mssalvatore\FileMirror\Test\Configuration;

use \mssalvatore\FileMirror\Actions\RsyncActionFactory;
use \mssalvatore\FileMirror\Actions\RsyncAction;
use \PHPUnit\Framework\TestCase;
use \org\bovigo\vfs\vfsStream;
use \org\bovigo\vfs\vfsStreamDirectory;
use \org\bovigo\vfs\vfsStreamWrapper;

class TestRsyncActionFactory extends RsyncActionFactory
{
    public function getRsyncClients()
    {
        return $this->rsyncClients;
    }
}

class RsyncActionFactoryTest extends TestCase
{
    protected function setUp()
    {
        vfsStreamWrapper::register();
        $vfsStreamDir = new vfsStreamDirectory('keys');
        vfsStreamWrapper::setRoot($vfsStreamDir);

        $this->key1 = vfsStream::url('keys/key1.pub');
        file_put_contents($this->key1, "");
        $this->key2 = vfsStream::url('keys/key2.pub');
        file_put_contents($this->key2, "");

        $this->config = (object) array(
            "servers" => array(
                (object) array (
                    "serverName" => "pegasus",
                    "user" => "cain",
                    "host" => "pegasus.battlestar",
                    "port" => 4422,
                    "publicKey" => $this->key1
                ),
                (object) array (
                    "serverName" => "galactica",
                    "user" => "adama",
                    "host" => "galactica.battlestar",
                    "publicKey" => $this->key2
                )
            )
        );

        $this->factory = new TestRsyncActionFactory($this->config);
    }

    public function testArchiveSet()
    {
        $clients = $this->factory->getRsyncClients();
        foreach ($clients as $client) {
            $this->assertEquals($client->getArchive(), true);
        }
    }

    public function testBuild()
    {
        $sourcePath = "/home/msalvatore/source1";
        $destinationPath = "/home/msalvatore/mirror/";
        $action = $this->factory->buildAction("galactica", $sourcePath, $destinationPath);

        $this->assertEquals($action->getSourcePath(), $sourcePath);
        $this->assertEquals($action->getDestinationPath(), $destinationPath);
    }
}
