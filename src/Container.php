<?php

namespace Supercluster\Gravity;

use Respect\Config\Container as ConfigContainer;
use Respect\Config\Instantiator;

class Container extends ConfigContainer
{
    protected function parseArrayItems(&$value)
    {
        foreach ($value as $key => &$subValue) {
            $subValue = $this->parseValue($subValue);

            if ($subValue instanceof Instantiator) {
                $subValue = $subValue->getInstance();
            }

        }
        return $value;
    }

    protected function parseStandardItem($key, &$value)
    {
        if (is_array($value)) {
            $this->parseArrayItems($value);
        } else {
            $value = $this->parseValue($value);
        }

        $this->offsetSet($key, $value);
    }
}
