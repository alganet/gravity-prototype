<?php

namespace Supercluster\Gravity\Datasources;

use Serializable;
use PDO;

class Connection extends PDO implements Serializable
{
    public function __construct($dsn, $username, $password)
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
