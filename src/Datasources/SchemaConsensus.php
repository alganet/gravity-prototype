<?php

namespace Supercluster\Gravity\Datasources;

use Serializable;
use Respect\Relational\Db;
use Respect\Rest\Routable;

class SchemaConsensus extends SchemaContribution implements Routable, Serializable
{

    public function serialize()
    {
        return serialize([$this->schemas, $this->database]);
    }

    public function unserialize($serialized)
    {
        list($this->schemas, $this->database) = unserialize($serialized);
    }

    /** @var array The schema consensus list **/
    protected $schemas = [];

    /** @var Respect\Relational\Db A database instance **/
    protected $database;

    /**
     * @param Respect\Relational\Db $database A database instance
     */
    public function __construct(Db $database)
    {
        $this->database = $database;
    }

    /**
     * Handle for Respect\Rest\Routable to receive EXEC HTTP method
     */
    public function exec()
    {
        $this->resetSchemaSpecification();
    }

    /**
     * Resets the schema specification to a consensus on the $database
     *
     * @return null
     */
    public function resetSchemaSpecification()
    {
        foreach ($this->ranges as $name => $types) {

            if (ucfirst($name) !== $name) {
                throw new \Exception("Broken consensus on '$name': invalid lowercase name");
            }

            if (!isset($this->types[$name])) {
                throw new \Exception("Broken consensus on '$name': no type definition");
            }

            echo "Applying schema for collection '{$name}'...\n";
            $columns = [];

            foreach ($types as $typeName) {

                if (!isset($this->types[$typeName])) {
                    throw new \Exception("Broken consensus on '$typeName': no type definition");
                }

                $columns[] = "{$typeName} {$this->types[$typeName]}";
            }

            $this->database->dropTableIfExists($name)->exec();
            $this->database->createTableIfNotExists($name, $columns)->exec();

            foreach (array_chunk($columns, 3) as $column_chunk) {
                echo implode(', ', $column_chunk) . PHP_EOL;
            }

            echo PHP_EOL;
        }
    }

    /**
     * Merges a new contribution to the consensus
     *
     * @param Supercluster\Gravity\Datasources\SchemaContribution $schema Contribution
     *
     * @return null
     */
    public function addSchema(SchemaContribution $schema)
    {
        $this->schemas[] = $schema;
        foreach ($schema->types as $name => $spec) {

            if (
                isset($this->types[$name]) &&
                $this->types[$name] != $spec
            ) {
                throw new \Exception("Broken consensus on '$name': '$spec' vs '{$this->types[$name]}'");
            }

            $this->subscribeType($name, $spec);
        }

        foreach ($schema->ranges as $name => $spec) {
            foreach ($spec as $range) {
                $this->subscribeRange($name, $range);
            }
        }
    }
}
