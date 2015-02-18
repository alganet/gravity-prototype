<?php

namespace Supercluster\Gravity\Datasources;

use Respect\Relational\Db;
use Respect\Rest\Routable;

class SchemaConsensus implements Routable
{
    protected $schemas = [];
    protected $database;

    public function __construct(Db $database)
    {
        $this->database = $database;
    }

    public function exec()
    {
        foreach ($this->schemas as $schema)
            print_r($schema);
    }

    public function addSchema(SchemaContribution $schema)
    {
        $this->schemas[] = $schema;
    }
}
