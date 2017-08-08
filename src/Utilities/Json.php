<?php

namespace mssalvatore\FileMirror\Utilities;

use mssalvatore\FileMirror\Exceptions\FileException;
use mssalvatore\FileMirror\Exceptions\FileNotFoundException;
use mssalvatore\FileMirror\Exceptions\JsonException;

/**
 * Reads JSON from a file and unmarshall as an object (stdClass), handling all errors as necessary
 *
 * @param   string  $filePath   The file to read json from
 *
 * @throws  FileException           If the provided $filePath could not be read
 * @throws  FileNotFoundException   If the provided $filePath doesn't exist
 * @throws  JsonException           If the provided $filePath does not contain valid JSON
 *
 * @return  \stdClass   The decoded json from the contents of $filePath
 */
function unmarshalJsonFile($filePath)
{
    if (! file_exists($filePath)) {
        throw new FileNotFoundException("The file '$filePath' could not be found");
    }

    $rawJson = @file_get_contents($filePath);

    if (! $rawJson) {
        throw new FileException("Could not read file '$filePath'");
    }

    $unmarshalledJson = json_decode($rawJson);

    if (is_null($unmarshalledJson)) {
        $lastError = json_last_error_msg();
        throw new JsonException("Unable to parse JSON in '$filePath': $lastError");
    }

    return $unmarshalledJson;
}
