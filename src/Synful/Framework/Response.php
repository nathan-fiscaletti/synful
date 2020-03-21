<?php

namespace Synful\Framework;

use Exception;
use Synful\Serializers\DownloadSerializer;
use Synful\Serializers\TextSerializer;

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
     * @var Serializer
     */
    public $serializer;

    /**
     * The request associated with this response.
     * 
     * @var Request
     */
    public $request;

    /**
     * Sets a header for the Response.
     *
     * @param string $header
     * @param string $value
     *
     * @return Response
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
    public function headers() : array
    {
        return $this->headers;
    }

    /**
     * Sets the Serializer class to use with this Response.
     *
     * @param Serializer $serializer
     *
     * @return Response
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
     * @return Response
     */
    public function downloadableAs(string $filename)
    {
        $this->setSerializer(new DownloadSerializer);
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

        if (! is_null($this->serializer)) {
            $serializer = $this->serializer;
        } else {
            $serializer = sf_conf('system.serializer');
            if (! is_null($serializer)) {
                $serializer = new $serializer;
            } else {
                $serializer = new TextSerializer;
            }
        }

        $ret = null;

        try {
            $ret = $serializer->serialize($this->response);
        } catch (Exception $e) {
            $ret = $e->getMessage();
        }

        return $ret;
    }
}
