<?php

namespace Supercluster\Gravity;

use Respect\Rest\Routable;
use Respect\Rest\AbstractRoute;

class Resource implements Routable
{
    public function __construct()
    {
    }

    public function get()
    {
        print_r($this->products->fetchAll());
    }

}
