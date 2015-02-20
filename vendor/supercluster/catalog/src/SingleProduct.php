<?php

namespace Supercluster\Catalog;

use Respect\Rest\Routable;
use Respect\Data\Collections\Collection;

class SingleProduct implements Routable
{
    protected $productsCollection;

    public function __construct(Collection $productsCollection)
    {
        $this->productsCollection = $productsCollection;
    }

    public function get()
    {
        $this->productsCollection->persist((object) [
            'id' => null,
        ]);
        //  $this->productsCollection->flush();
        return $this->productsCollection->fetchAll();
    }
}
