<?php

namespace Supercluster\Gravity\Datasources;

use Respect\Data\Styles\Standard;

/**
 * Supercluster database naming convention
 *
 * @see Respect\Data\AbstractMapper\setStyle
 */
class NamingConvention extends Standard
{

    /**
     * @param  string $name Local name for a collection
     * @return string Name of the foreign identifier for a table
     */
    public function remoteIdentifier($name)
    {
        return $name;
    }

    /**
     * @param  string $name Name of a column
     * @return bool   True if name is a remote identifier, false otherwise
     */
    public function isRemoteIdentifier($name)
    {
        return (ucfirst($name) === $name);
    }

    /**
     * @param  string Name of the foreign identifier for a table
     * @return string $name Local name for a collection
     */
    public function remoteFromIdentifier($name)
    {
        if ($this->isRemoteIdentifier($name)) {
            return $name;
        }
    }

}
