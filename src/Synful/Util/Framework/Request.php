<?php

namespace Synful\Util\Framework;

/**
 * Class used to represent an HTTP request.
 */
class Request implements \ArrayAccess {
    use Object;

    /**
     * The request data.
     *
     * @var array
     */
    private $data;

    /**
     * The request headers.
     *
     * @var array
     */
    public $headers;

    /**
     * The email associated with the key of the client making the request.
     *
     * @var string
     */
    public $email;

    /**
     * The IP address of the client that sent the request.
     *
     * @var string
     */
    public $ip;

    /**
     * Override for ArrayAccess offsetExists.
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Override for ArrayAccess offsetGet.
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet ($offset)
    {
        return $this->data[$offset];
    }

    /**
     * Override for ArrayAccess offsetSet.
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('Cannot modify a request object.');
    }

    /**
     * Override for ArrayAccess offsetUnset.
     *
     * @param  mixed $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('Cannot modify a request object.');
    }

    /**
     * Override magic __get function.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new \Exception('Call to undefined property '.$name.'.');
    }

    /**
     * Override magic __set function.
     *
     * @param  string $name
     * @param  string $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        throw new \Exception('Cannot modify a request object.');   
    }

    /**
     * Override magic __call function.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        if (count($args) > 0) {
            throw new \Exception('Cannot modify a request object.');          
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new \Exception('Call to undefined function '.$name.'.');
    }
}