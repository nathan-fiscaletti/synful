<?php
    
namespace Synful;

use JsonSerializable;
use Synful\Util\Object;

/**
 * Class used for response storage
 */
class Response implements JsonSerializable
{
    use Object;

    /**
     * The request that this response is for
     *
     * @var array
     */
    public $request;

    /**
     * The HTTP Response Code
     *
     * @var integer
     */
    public $code;

    /**
     * The response data that will be serialized and set back to client
     *
     * @var array
     */
    public $response;

    /**
     * The IP address of the client making the request
     *
     * @var string
     */
    public $requesting_ip;

    /**
     * The email associated with the key of the client making the request
     *
     * @var string
     */
    public $requesting_email;

    /**
     * Overrides full response object with custom array of data
     *
     * @param Array $data
     */
    public function overloadResponse(array $data)
    {
        $this->response = $data;
    }

    /**
     * Add a list of responses to the response object
     *
     * @param Array $responses
     */
    public function addResponses(array $responses)
    {
        $this->response = array_merge($this->response, $responses);
    }

    /**
     * Adds data to the data variable that will be returned with the object
     *
     * @param String $key
     * @param mixed $data
     */
    public function setResponse($key, $data)
    {
        $this->response[$key] = $data;
    }

    /**
     * Override serialization for json_encode to ommit $request variable
     *
     * @return Array
     */
    public function jsonSerialize()
    {
        return [ 'code' => $this->code, 'response' => $this->response ];
    }
}
