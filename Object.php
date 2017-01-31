<?php

namespace vendor\i4h\PhpMIP;
/**
 *
 * Implements yii-like magic getters and setters
 *
 */

class Object {

    public function __get($property)
    {

        if (!in_array($property, $this->magic))
            trigger_error("Property $property does not exist.", E_USER_ERROR);

        $call = "get".ucfirst($property);
        $result = $this->{$call}();
        return $result;
    }

    public function __set($property, $value)
    {
        if (!in_array($property, $this->magic))
            trigger_error("Property $property does not exist and cannot be set.", E_USER_ERROR);

        throw new \Exception(("test me"));
        $call = "set".ucfirst($property);
        $this->{$call}($value);
    }
}