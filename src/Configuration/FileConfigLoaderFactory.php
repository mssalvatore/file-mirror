<?php

namespace mssalvatore\FileMirror\Configuration;

use \mssalvatore\FileMirror\Exceptions\ConfigurationException;
use \mssalvatore\FileMirror\Utilities;

class FileConfigLoaderFactory
{
    const CONFIG_SCHEMA_PATH = __DIR__ . "/../../config/schema/config.schema.json";
    const SERVER_SCHEMA_PATH = __DIR__ . "/../../config/schema/server.schema.json";
    const SERVER_SCHEMA_NAME = "file://server.schema.json";

    protected $configFilePath;
    protected $schema;
    protected $jsonValidator;

    public function __construct()
    {
        $this->schema = Utilities\UnmarshalJsonFile(self::CONFIG_SCHEMA_PATH);
        $serverSchema = Utilities\UnmarshalJsonFile(self::SERVER_SCHEMA_PATH);

        $schemaStorage = new \JsonSchema\SchemaStorage();
        $schemaStorage->addSchema(self::SERVER_SCHEMA_NAME, $serverSchema);

        $this->jsonValidator = new \JsonSchema\Validator(new \JsonSchema\Constraints\Factory($schemaStorage));
    }

    public function setConfigFilePath($configFilePath)
    {
        $this->configFilePath = $configFilePath;
    }

    public function buildConfigLoader()
    {
        if (empty($this->configFilePath)) {
            throw new ConfigurationException("Cannot build file config loader: No configuration file has been specified");
        }

        return new FileConfigLoader($this->jsonValidator, $this->schema, $this->configFilePath);
    }
}
