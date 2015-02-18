<?php

namespace Supercluster\Gravity;

use Respect\Rest\Routes\Factory;
use Respect\Config\Instantiator;

/**
 * A route that uses a container instantiator to lazy load its dependencies
 */
class Route extends Factory
{
    public function __construct($method, $pattern, Instantiator $factory)
    {
        parent::__construct(
            $method,
            $pattern,
            $factory->getClassName(),
            $factory
        );
    }
}
