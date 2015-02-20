<?php

namespace Supercluster\Gravity;

use Respect\Rest\Routable;
use Respect\Rest\AbstractRoute;

class Resource implements Routable
{
    public function __construct($products)
    {
        $this->products = $products;
    }

    public function get()
    {
        return ($this->products->fetchAll());
    }

}
