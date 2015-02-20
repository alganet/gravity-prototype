<?php

namespace Supercluster\Gravity\Datasources;

use Respect\Relational\Db;
use Respect\Rest\Routable;

class SchemaConsensus extends SchemaContribution implements Routable
{
    protected $schemas = [];
    protected $database;

    public function __construct(Db $database)
    {
        $this->database = $database;
    }

    public function exec()
    {
        foreach ($this->collections as $name => $properties) {

            if (ucfirst($name) !== $name) {
                throw new \Exception("Broken consensus on '$name': invalid lowercase name");
            }

            if (!isset($this->properties[$name])) {
                throw new \Exception("Broken consensus on '$name': no type definition");
            }

            echo "Applying schema for collection '{$name}'...\n";
            $columns = [];

            foreach ($properties as $propertyName) {

                if (!isset($this->properties[$propertyName])) {
                    throw new \Exception("Broken consensus on '$property': no type definition");
                }

                $columns[] = "{$propertyName} {$this->properties[$propertyName]}";
            }

            $this->database->dropTableIfExists($name)->exec();
            $this->database->createTableIfNotExists($name, $columns)->exec();

            foreach (array_chunk($columns, 3) as $column_chunk) {
                echo implode(', ', $column_chunk) . PHP_EOL;
            }

            echo PHP_EOL;
        }
    }

    public function addSchema(SchemaContribution $schema)
    {
        $this->schemas[] = $schema;
        foreach ($schema->properties as $name => $spec) {

            if (
                isset($this->properties[$name]) &&
                $this->properties[$name] != $spec
            ) {
                throw new \Exception("Broken consensus on '$name': '$spec' vs '{$this->properties[$name]}'");
            }

            $this->subscribeType($name, $spec);
        }

        foreach ($schema->collections as $name => $spec) {
            foreach ($spec as $range) {
                $this->subscribeRange($name, $range);
            }
        }
    }
}
