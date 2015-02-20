<?php

namespace Supercluster\Gravity;

use Serializable;
use ReflectionClass;
use Respect\Config\Instantiator as ConfigInstantiator;

class LazyInstantiator extends ConfigInstantiator implements Serializable
{
    public function __construct($className = '')
    {
        if ($className) {
            $this->reflection = new ReflectionClass($className);
            $this->constructor = $this->findConstructorParams($this->reflection);
            $this->className = $className;
        }
    }

    public function getInstance($forceNew = false)
    {
        if ($this->className) {
            return parent::getInstance($forceNew);
        }
        foreach ($this->propertySetters as $key => &$value) {
            if ($value instanceof ConfigInstantiator) {
                $value = $value->getInstance();
            }
        }
        return $this->propertySetters;
    }

    protected function lazyLoad($value)
    {
        return $value instanceof self ? $value->getInstance() : $value;
    }


    public function setParam($key, $param)
    {
        if ($this->className) {
            return parent::setParam($key, $param);
        }

        $this->propertySetters[$key] = $param;
    }

    public function serialize()
    {

    }

    public function unserialize($serialized)
    {

    }
}
