<?php

namespace Supercluster\Gravity\Datasources;

/**
 * A single contribution to an overall database schema
 */
class SchemaContribution
{
    protected $properties  = [];
    protected $collections = [];

    /**
     * @param  string $name Name of a property
     * @param  string $spec SQL specification for the type
     *
     * @return null
     */
    public function subscribeProperty($name, $spec)
    {
        $this->properties[$name] = $spec;
    }

    /**
     * @param  string $name     Name of a collection
     * @param  string $property Property name to be subscribed
     *
     * @return null
     */
    public function subscribeRange($name, $property)
    {
        if (!isset($this->collections[$name])) {
            $this->collections[$name] = [];
        }
        $this->collections[$name][] = $property;
    }
}
