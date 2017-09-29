<?php

namespace mssalvatore\FileMirror\Utilities;

use \mssalvatore\FileMirror\Exceptions\JsonException;
use \mssalvatore\FileMirror\Exceptions\ConfigurationException;

class ConfigLoader
{
    protected $validator;
    protected $configSchema;
    protected $configFilePath;
    protected $config;

    public function __construct(\JsonSchema\Validator $validator, \stdClass $configSchema, $configFilePath)
    {
        $this->validator = $validator;
        $this->configSchema = $configSchema;
        $this->configFilePath = $configFilePath;
    }

    public function loadConfig()
    {
        $this->config = UnmarshalJsonFile($this->configFilePath);
        $this->throwOnSchemaValidationFailure();
        $this->throwOnInvalidConfigOptions();

        return $this->config;
    }

    protected function throwOnSchemaValidationFailure()
    {
        $this->validator->validate($this->config, $this->configSchema);

        if (!$this->validator->isValid()) {
            $errorMessage = $this->validator->getErrors()[0];
            throw new JsonException('Unable to validate JSON against the provided schema -- Property: "' . $errorMessage['property'] . '", Message: "' . $errorMessage['message'] . "\"");
        }
    }

    protected function throwOnInvalidConfigOptions()
    {
        $this->throwOnInvalidMirrorFileSet();
    }

    protected function throwOnInvalidMirrorFileSet()
    {
        $this->throwIfInvalidServerSpecified();
    }

    protected function throwIfInvalidServerSpecified()
    {
        $i = 1;
        foreach ($this->config->mirrorFiles as $mf) {
            foreach ($mf->serverNames as $serverName) {
                if (! $this->serverExists($serverName)) {
                    throw new ConfigurationException("The server name \"$serverName\" specified in mirror file set $i is not defined in the \"servers\" section of the configuration");
                }
            }

            $i++;
        }

        return false;
    }

    protected function serverExists($serverName)
    {
        foreach ($this->config->servers as $server) {
            if ($server->serverName == $serverName) {
                return true;
            }
        }

        return false;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
