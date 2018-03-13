<?php

namespace Synful\Util\Framework;

/**
 * Class used to represent an HTTP request.
 */
class Request
{
    use Object;

    /**
     * The request data.
     *
     * @var array
     */
    private $data;

    /**
     * The request fields in the URL.
     *
     * @var array
     */
    private $fields;

    /**
     * The request headers.
     *
     * @var array
     */
    public $headers;

    /**
     * The authentication handle associated with the key of the client making the request.
     *
     * @var string
     */
    public $auth;

    /**
     * The IP address of the client that sent the request.
     *
     * @var string
     */
    public $ip;

    /**
     * The request method used in the HTTP request.
     *
     * @var string
     */
    public $method;

    /**
     * Returns a field from the request path.
     *
     * @param  string $name
     * @return mixed
     */
    public function field($name)
    {
        $ret = null;

        if (array_key_exists($name, $this->fields)) {
            $ret = $this->fields[$name];
        }

        return $ret;
    }

    /**
     * Returns a array of all fields for this request.
     *
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Retrieve input from the request.
     *
     * @param  string $path
     * @return mixed
     */
    public function input($path)
    {
        $keys = explode('.', $path);
        $final_key = array_pop($keys);
        $result = $this->data;

        foreach ($keys as $key) {
            if (! array_key_exists($key, $result)) {
                return;
            }

            $result = $result[$key];

            if (! is_array($result)) {
                throw new SynfulException(
                    500,
                    1019,
                    'Invalid path element. \''.
                    substr($path, 0, strpos($path, $key) + strlen($key)).
                    '\' is not a valid array.'
                );
            }
        }

        if (! array_key_exists($final_key, $result)) {
            return;
        }

        return $result[$final_key];
    }

    /**
     * Returns a list of all inputs for this request.
     *
     * @return array
     */
    public function inputs()
    {
        return $this->data;
    }
}
