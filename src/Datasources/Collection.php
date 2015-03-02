<?php

namespace Supercluster\Gravity\Datasources;

use Serializable;
use Respect\Data\Collections\Collection as DataCollection;

/**
 * A flushable collection.
 */
class Collection extends DataCollection
{
    public function flush()
    {
        if (!$this->mapper) {
            throw new \RuntimeException;
        }

        return $this->mapper->flush();
    }
}
