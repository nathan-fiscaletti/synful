<?php

namespace Synful\Util;

use Exception;

/**
 * Trait used as a base for classes with constructors that match their properties.
 */
trait Object
{
    /**
     * Automatically define parameters for object.
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } else {
                throw new Exception('Unknown property \''.$key.'\' in Object definition.');
            }
        }
    }

    /**
     * Handle undefined function calls as property access.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $ret = $this;
        if (property_exists($this, $name)) {
            if (count($arguments) < 1) {
                $ret = $this->{$name};
            } else {
                $this->{$name} = $arguments[0];
            }
        } else {
            throw new Exception('Call to undefined function \''.$name.'\'.');
        }

        return $ret;
    }
}
