<?php

namespace Supercluster\Gravity\MediaTypes;

/**
 * Represents data as JSON
 */
class ApplicationJson
{
    public function __invoke($data)
    {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}
