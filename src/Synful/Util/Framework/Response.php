<?php

namespace Synful\Util\Framework;

use JsonSerializable;

/**
 * Class used for response storage.
 */
class Response implements JsonSerializable
{
    use Object;

    /**
     * The request that this response is for.
     *
     * @var array
     */
    public $request;

    /**
     * The HTTP Response Code.
     *
     * @var int
     */
    public $code;

    /**
     * The response data that will be serialized and set back to client.
     *
     * @var array
     */
    public $response;

    /**
     * The IP address of the client making the request.
     *
     * @var string
     */
    public $requesting_ip;

    /**
     * The email associated with the key of the client making the request.
     *
     * @var string
     */
    public $requesting_email;

    /**
     * If set to true, the response will be encrypted based on the encryption
     * functions defined in the security section of the config.
     *
     * @var bool
     */
    public $encrypt_response = false;

    /**
     * Overrides full response object with custom array of data.
     *
     * @param array $data
     */
    public function overloadResponse(array $data)
    {
        $this->response = $data;
    }

    /**
     * Add a list of responses to the response object.
     *
     * @param array $responses
     */
    public function addResponses(array $responses)
    {
        $this->response = array_merge($this->response, $responses);
    }

    /**
     * Adds data to the data variable that will be returned with the object.
     *
     * @param string $key
     * @param mixed $data
     */
    public function setResponse($key, $data)
    {
        $this->response[$key] = $data;
    }

    /**
     * Override serialization for json_encode to ommit $request variable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->response;
    }

    /**
     * Get the serialized version of the response.
     * Encryption will be used if needed.
     *
     * @return string
     */
    public function serialize()
    {
        $ret = json_encode($this);

        if (sf_conf('system.pretty_responses') || (isset($_GET['pretty'])
            && Synful::$config->get('system.allow_pretty_responses_on_get'))) {
            $ret = json_encode($this, JSON_PRETTY_PRINT);
        }

        if ($this->encrypt_response || sf_conf('security.force_encryption')) {
            $ret = sf_encrypt($ret);
        }

        return $ret;
    }
}
