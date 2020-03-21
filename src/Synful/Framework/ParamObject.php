<?php

namespace Synful\Framework;

/**
 * Trait used as a base for classes with constructors that match their properties.
 */
trait ParamObject
{
    /**
     * Automatically define parameters for object.
     *
     * @param array $params
     * @throws SynfulException
     */
    public function __construct($params = [])
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } else {
                throw new SynfulException(
                    500,
                    -1,
                    'Unknown property \''.$key.'\' in Object definition.'
                );
            }
        }
    }

    /**
     * Handle undefined function calls as property access.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws SynfulException
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
            throw new SynfulException(
                500,
                -2,
                'Call to undefined function \''.$name.'\'.'
            );
        }

        return $ret;
    }
}
