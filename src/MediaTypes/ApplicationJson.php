<?php

namespace Supercluster\Gravity\MediaTypes;

class ApplicationJson
{
    public function __invoke($data)
    {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}
