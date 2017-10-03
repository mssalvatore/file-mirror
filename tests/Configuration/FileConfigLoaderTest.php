<?php

namespace mssalvatore\FileMirror\Test\Configuration;

use \mssalvatore\FileMirror\Configuration\FileConfigLoader;
use \mssalvatore\FileMirror\Exceptions\JsonException;
use \PHPUnit\Framework\TestCase;
use \org\bovigo\vfs\vfsStream;
use \org\bovigo\vfs\vfsStreamDirectory;
use \org\bovigo\vfs\vfsStreamWrapper;

class FileConfigLoaderTest extends TestCase
{
    protected function setUp()
    {
        vfsStreamWrapper::register();
        $vfsStreamDir = new vfsStreamDirectory('config');
        vfsStreamWrapper::setRoot($vfsStreamDir);

        $this->validFileUrl = vfsStream::url('config/valid.json');
        file_put_contents($this->validFileUrl, $this->validConfig, 0644);

        $this->mockValidator = $this->createMock(\JsonSchema\Validator::class);
        $this->mockSchema = new \stdClass();
    }

    protected function setupValidConfig()
    {
        $this->mockValidator->expects($this->exactly(1))->method("validate");
        $this->mockValidator->expects($this->exactly(1))->method("isValid")->willReturn(true);
        $this->configLoader = new FileConfigLoader($this->mockValidator, $this->mockSchema, $this->validFileUrl);
        $this->configLoader->loadConfig();

        $this->config = $this->configLoader->getConfig();
    }

    public function testValidConfigLoadsServers()
    {
        $this->setupValidConfig();

        $this->assertEquals(count($this->config->servers), 2);

        $this->assertEquals($this->config->servers[0]->serverName, "galactica");
        $this->assertEquals($this->config->servers[0]->user, "adama");
        $this->assertEquals($this->config->servers[0]->host, "galactica.battlestar");
        $this->assertEquals($this->config->servers[0]->publicKey, "~/.ssh/galactica_key.pub");

        $this->assertEquals($this->config->servers[1]->serverName, "pegasus");
        $this->assertEquals($this->config->servers[1]->user, "cain");
        $this->assertEquals($this->config->servers[1]->host, "pegasus.battlestar");
        $this->assertEquals($this->config->servers[1]->port, 4422);
        $this->assertEquals($this->config->servers[1]->publicKey, "~/.ssh/pegasus_key.pub");
    }

    public function testValidConfigMirrorFiles()
    {
        $this->setupValidConfig();

        $this->assertEquals(count($this->config->mirrorFiles), 2);
        
        $this->assertEquals($this->config->mirrorFiles[0]->serverNames, array("galactica", "pegasus"));
        $this->assertEquals($this->config->mirrorFiles[0]->files, array("/home/adama/f1"));
        $this->assertEquals($this->config->mirrorFiles[0]->destinationDirectory, "/home/adama/mirror");

        $this->assertEquals($this->config->mirrorFiles[1]->serverNames, array("pegasus"));
        $this->assertEquals($this->config->mirrorFiles[1]->files, array("/home/cain/f1", "/home/cain/f2"));
        $this->assertEquals($this->config->mirrorFiles[1]->destinationDirectory, "/home/cain/mirror");
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\JsonException
     * @expectedExceptionMessage Unable to validate JSON against the provided schema -- Property: "serverName", Message: "mysterious error"
     */
    public function testInvalidSchema()
    {
        $this->mockValidator->expects($this->exactly(1))->method("validate");
        $this->mockValidator->expects($this->exactly(1))->method("isValid")->willReturn(false);
        $this->mockValidator->expects($this->exactly(1))->method("getErrors")->willReturn(array(array("property" => "serverName", "message" => "mysterious error")));
        $this->configLoader = new FileConfigLoader($this->mockValidator, $this->mockSchema, $this->validFileUrl);
        $this->configLoader->loadConfig();
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\ConfigurationException
     * @expectedExceptionMessage The server name "enterprise" specified in mirror file set 2 is not defined in the "servers" section of the configuration
     *
     */
    public function testInvalidServerSpecified()
    {
        $this->mockValidator->expects($this->any())->method("validate");
        $this->mockValidator->expects($this->any())->method("isValid")->willReturn(true);
        $this->configLoader = new FileConfigLoader($this->mockValidator, $this->mockSchema, $this->validFileUrl);
        $this->config = $this->configLoader->loadConfig();

        array_push($this->config->mirrorFiles[1]->serverNames, "enterprise");
        file_put_contents($this->validFileUrl, json_encode($this->config));

        $this->configLoader->loadConfig();
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\ConfigurationException
     * @expectedExceptionMessage The server name "pegasus" is duplicated in the "servers" section of the configuration
     *
     */
    public function testDuplicateServers()
    {
        $this->mockValidator->expects($this->any())->method("validate");
        $this->mockValidator->expects($this->any())->method("isValid")->willReturn(true);
        $this->configLoader = new FileConfigLoader($this->mockValidator, $this->mockSchema, $this->validFileUrl);
        $this->config = $this->configLoader->loadConfig();

        $this->config->servers[2] = $this->config->servers[1];
        file_put_contents($this->validFileUrl, json_encode($this->config));

        $this->configLoader->loadConfig();
    }



private $validConfig = <<<VALID
{
    "servers": [
        {
            "serverName": "galactica",
            "user": "adama",
            "host": "galactica.battlestar",
            "publicKey": "~/.ssh/galactica_key.pub"
        },
        {
            "serverName": "pegasus",
            "user": "cain",
            "host": "pegasus.battlestar",
            "port": 4422,
            "publicKey": "~/.ssh/pegasus_key.pub"
        }
    ],
    "mirrorFiles": [
        {
            "serverNames": ["galactica", "pegasus"],
            "files": ["/home/adama/f1"],
            "destinationDirectory": "/home/adama/mirror"
        },
        {
            "serverNames": ["pegasus"],
            "files": ["/home/cain/f1", "/home/cain/f2"],
            "destinationDirectory": "/home/cain/mirror"
        }
    ]
}
VALID;
}
