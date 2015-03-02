<?php

namespace Supercluster\Gravity\Configuration;

use Serializable;
use Respect\Rest\Routes\Factory;

/**
 * A route that uses a container instantiator to lazy load its dependencies
 */
class Route extends Factory implements Serializable
{
    public function __construct($method, $pattern, LazyInstantiator $factory)
    {
        parent::__construct(
            $method,
            $pattern,
            $factory->getClassName(),
            $factory
        );
    }

    public function serialize()
    {
        return serialize([
            $this->method,
            $this->pattern,
            $this->class,
            $this->factory
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->method,
            $this->pattern,
            $this->class,
            $this->factory
        ) = unserialize($serialized);
        parent::__construct($this->method, $this->pattern, $this->class, $this->factory);
    }
}
