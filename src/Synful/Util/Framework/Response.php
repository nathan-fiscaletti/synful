<?php

namespace Synful\Util\Framework;

/**
 * Class used for response storage.
 */
class Response
{
    use ParamObject;

    /**
     * The response headers.
     *
     * @var array
     */
    public $headers = [];

    /**
     * The HTTP Response Code.
     *
     * @var int
     */
    public $code;

    /**
     * The response data that will be serialized and sent back to client.
     *
     * @var array
     */
    public $response;

    /**
     * The serializer to use.
     *
     * @var \Synful\Util\Framework\Serializer
     */
    public $serializer;

    /**
     * Sets a header for the Response.
     *
     * @param string $header
     * @param string $value
     */
    public function setHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
    }

    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get the serialized version of the response.
     *
     * @return string
     */
    public function serialize()
    {
        $serializer = null;

        if ($this->serializer != null) {
            $serializer = $this->serializer;
        } else {
            $serializer = sf_conf('system.serializer');
            $serializer = new $serializer;
        }

        $ret = null;

        try {
            $ret = $serializer->serialize($this->response);
        } catch (\Exception $e) {
            $ret = $e->getMessage();
        }

        return $ret;
    }
}
