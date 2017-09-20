<?php

namespace mssalvatore\FileMirror\Test\Utilities;

use \PHPUnit\Framework\TestCase;
use function \mssalvatore\FileMirror\Utilities\unmarshalJsonFile;

class JsonTest extends TestCase
{
    protected function setUp()
    {
        $this->jsonObject = (object) array("testProperty1" => "Hello", "testProperty2" => "World", "testProperty3" => "!");

        $this->configDirectory = __DIR__ . "/../testConfigs";

        chmod($this->configDirectory . "/unreadableFile.json", 0000);
    }

    public function testJsonUnmarshall()
    {
        $unmarshalled = unmarshalJsonFile($this->configDirectory . "/validConfig.json");

        $this->assertEquals($this->jsonObject, $unmarshalled);
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\FileNotFoundException
     * @expectedExceptionMessageRegExp /The file '(\/+.+)+\/no_existe_mas.json' could not be found/
     */
    public function testFileNotFound()
    {
        unmarshalJsonFile("{$this->configDirectory}/no_existe_mas.json");
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\FileException
     * @expectedExceptionMessageRegExp /Could not read file '(\/+.+)+\/unreadableFile.json'/
     */
    public function testUnreadableFile()
    {
        unmarshalJsonFile("{$this->configDirectory}/unreadableFile.json");
    }

    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\JsonException
     * @expectedExceptionMessageRegExp /Unable to parse JSON in '(\/+.+)+\/invalidConfig.json': Syntax error/
     */
    public function testInvalidConfig()
    {
        unmarshalJsonFile("{$this->configDirectory}/invalidConfig.json");
    }


    protected function tearDown()
    {
        chmod($this->configDirectory . "/unreadableFile.json", 0640);
    }
}
