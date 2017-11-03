<?php

namespace mssalvatore\FileMirror\Test\Configuration;

use \mssalvatore\FileMirror\Configuration\FileConfigLoaderFactory;
use \mssalvatore\FileMirror\Exceptions\JsonException;
use \PHPUnit\Framework\TestCase;

class FileConfigLoaderFactoryTest extends TestCase
{
    /**
     * @expectedException   \mssalvatore\FileMirror\Exceptions\ConfigurationException
     * @expectedExceptionMessage Cannot build file config loader: No configuration file has been specified
     *
     */
    public function testBuildEmptyConfig()
    {
        $fileConfigLoaderFactory = new FileConfigLoaderFactory();

        $fileConfigLoaderFactory->buildConfigLoader();
    }

    public function testSuccessfulBuild()
    {
        try {
            $fileConfigLoaderFactory = new FileConfigLoaderFactory();
            $fileConfigLoaderFactory->setConfigFilePath("fake_path");
            $fileConfigLoaderFactory->buildConfigLoader();
        } catch (\Exception $ex) {
            $this->fail("Failed asserting that config loader was built successfuly");
        }
        $this->assertTrue(true);
    }
}

