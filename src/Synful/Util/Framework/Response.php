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
     * Override serialization for json_encode.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->response;
    }

    /**
     * Get the serialized version of the response.
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

        if ($this->response == null) {
            $ret = '';
        }

        return $ret;
    }
}
