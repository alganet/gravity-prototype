<?php

namespace Supercluster\Gravity\Configuration;

use Serializable;
use Respect\Config\Container as ConfigContainer;
use Respect\Config\Instantiator;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Interop\Container\ContainerInterface;

/**
 * An interoperable dependency injection container. It keeps lazy declarations
 * even when serialized and implements a standard to interface with other
 * containers.
 */
class InteropContainer
extends ConfigContainer
implements ContainerInterface, Serializable
{
    public function serialize()
    {
        $copy = $this->getArrayCopy();
        return serialize($copy);
    }

    public function unserialize($serialized)
    {
        $this->exchangeArray(unserialize($serialized));
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Gravity could not find '$id'.");
        }
        return $this->offsetGet($id);
    }

    public function has($id)
    {
        return (bool) $this->offsetExists($id);
    }

    /**
     * Creates a lazy reference to an array possibly containing instances.
     *
     * @param mixed $value A reference inside method parseStandardItem.
     *
     * @see parseStandardItem Method which injects the &$value reference.
     */
    protected function parseArrayItems(&$value)
    {
        $newValue = new LazyInstantiator();
        foreach ($value as $key => $subValue) {
            $newValue->setParam($key, $this->parseValue($subValue));
        }
        $value = $newValue->getInstance();
        return $value;
    }

    /**
     * Overrides parent method. Allows detecting and making arrays lazy
     * by calling parseArrayItems when necessary.
     *
     * @param string $key  Key used to store this item on the container.
     * @param mixed $value A reference inside method parseStandardItem.
     */
    protected function parseStandardItem($key, &$value)
    {
        if (is_array($value)) {
            $value = $this->parseArrayItems($value);
        } else {
            $value = $this->parseValue($value);
        }

        $this->offsetSet($key, $value);
    }


    /**
     * Overrides parent method. Allows using LazyInstantiators instead
     * of default ones.
     *
     * @param string $key  Key used to store this item on the container.
     * @param mixed $value The instantiator params.
     */
    protected function parseInstantiator($key, $value)
    {
        $key = $this->removeDuplicatedSpaces($key);
        list($keyName, $keyClass) = explode(' ', $key, 2);

        if ('instanceof' === $keyName) {
            $keyName = $keyClass;
        }

        $instantiator = new LazyInstantiator($keyClass);

        if (is_array($value)) {
            foreach ($value as $property => $pValue) {
                $instantiator->setParam($property, $this->parseValue($pValue));
            }
        } else {
            $instantiator->setParam('__construct', $this->parseValue($value));
        }

        $this->offsetSet($keyName, $instantiator);
    }
}
