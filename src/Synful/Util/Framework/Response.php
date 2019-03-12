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
    private $headers = [];

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
     *
     * @return \Synful\Util\Framework\Response
     */
    public function setHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * Retrieve the value for a header.
     *
     * @param string $header
     *
     * @return string
     */
    public function header(string $header)
    {
        return $this->headers[$header];
    }

    /**
     * Retrieve all headers for this Response.
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Sets the Serializer class to use with this Response.
     *
     * @param \Synful\Util\Framework\Serializer $serializer
     *
     * @return \Synful\Util\Framework\Response
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Makes this response a download.
     * 
     * @param string $filename
     *
     * @return \Synful\Util\Framework\Response
     */
    public function downloadableAs(string $filename)
    {
        $this->setSerializer(new \Synful\Util\Serializers\DownloadSerializer);
        $this->setHeader('Content-disposition', 'attachment; filename='.$filename);

        return $this;
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
