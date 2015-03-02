<?php

namespace Supercluster\Gravity;

use Serializable;
use Respect\Config\Container as ConfigContainer;
use Respect\Config\Instantiator;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Interop\Container\ContainerInterface;

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

    protected function parseArrayItems(&$value)
    {
        $newValue = new LazyInstantiator();
        foreach ($value as $key => $subValue) {
            $newValue->setParam($key, $this->parseValue($subValue));
        }
        $value = $newValue->getInstance();
        return $value;
    }

    protected function parseStandardItem($key, &$value)
    {
        if (is_array($value)) {
            $value = $this->parseArrayItems($value);
        } else {
            $value = $this->parseValue($value);
        }

        $this->offsetSet($key, $value);
    }


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
