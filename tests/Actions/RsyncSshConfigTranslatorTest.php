<?php

namespace mssalvatore\FileMirror\Test\Actions;

use \mssalvatore\FileMirror\Actions\RsyncSshConfigTranslator;
use \PHPUnit\Framework\TestCase;

class RsyncSshConfigTranslatorTest extends TestCase
{
    protected function setUp()
    {
        $this->defaultPort = 22;

        $this->publicKeyPort = (object) array(
            "serverName" => "pegasus",
            "user" => "cain",
            "host" => "pegasus.battlestar",
            "port" => 4422,
            "publicKey" => "~/.ssh/pegasus_key.pub"
        );

        $this->publicKeyNoPort = (object) array(
            "serverName" => "pegasus",
            "user" => "cain",
            "host" => "pegasus.battlestar",
            "publicKey" => "~/.ssh/pegasus_key.pub"
        );

        $this->translator = new RsyncSshConfigTranslator();
    }

    public function testUserName()
    {
        $sshConfig = $this->translator->translateSshConfiguration($this->publicKeyPort);

        $this->assertEquals($sshConfig['username'], $this->publicKeyPort->user);
    }

    public function testHost()
    {
        $sshConfig = $this->translator->translateSshConfiguration($this->publicKeyPort);

        $this->assertEquals($sshConfig['host'], $this->publicKeyPort->host);
    }
    public function testPort()
    {
        $sshConfig = $this->translator->translateSshConfiguration($this->publicKeyPort);

        $this->assertEquals($sshConfig['port'], $this->publicKeyPort->port);
    }
    public function testPublicKey()
    {
        $sshConfig = $this->translator->translateSshConfiguration($this->publicKeyPort);

        $this->assertEquals($sshConfig['public_key'], $this->publicKeyPort->publicKey);
    }

    public function testDefaultPort()
    {
        $sshConfig = $this->translator->translateSshConfiguration($this->publicKeyNoPort);

        $this->assertEquals($sshConfig['port'], $this->defaultPort);
    }

}
