<?php

namespace Supercluster\Gravity\Datasources;

use Serializable;
use PDO;

/**
 * Serializable implementation for PDO keeping configuration on its state.
 */
class Connection extends PDO implements Serializable
{
    public function __construct($dsn, $username = null, $password = null)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;

        parent::__construct($dsn, $username, $password);
    }

    public function serialize()
    {
        return serialize([$this->dsn, $this->username, $this->password]);
    }

    public function unserialize($serialized)
    {
        list($dsn, $username, $password) = unserialize($serialized);

        parent::__construct($dsn, $username, $password);
    }
}
