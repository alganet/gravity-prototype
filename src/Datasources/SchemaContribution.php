<?php

namespace Supercluster\Gravity\Datasources;

/**
 * A single contribution to an overall database schema
 */
class SchemaContribution
{
    protected $types  = [];
    protected $ranges = [];

    /**
     * Contribute to a type name and its specification
     *
     * @param  string $name Name of a type
     * @param  string $spec Specification for the type
     *
     * @return null
     */
    public function subscribeType($name, $spec)
    {
        $this->types[$name] = $spec;
    }

    /**
     * Contribute to a type range
     *
     * @param  string $name     Name of a collection
     * @param  string $range    Range name to be subscribed
     *
     * @return null
     */
    public function subscribeRange($name, $range)
    {
        if (!isset($this->ranges[$name])) {
            $this->ranges[$name] = [];
        }

        $this->ranges[$name][] = $range;
    }
}
