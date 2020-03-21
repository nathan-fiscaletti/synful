<?php

namespace Synful\Serializers;

use Synful\Framework\Serializer;
use Synful\Framework\SynfulException;

class JSONSerializer implements Serializer
{
    /**
     * The content type for the serialized data.
     *
     * @var string
     */
    public $mime_type = 'application/json';

    /**
     * Serialize the data to be sent back to the client.
     *
     * @param  array  $data
     * @return string
     */
    public function serialize(array $data) : string
    {
        $ret = json_encode($data);

        if (sf_conf('system.pretty_responses') || (isset($_GET['pretty'])
            && sf_conf('system.allow_pretty_responses_on_get'))) {
            $ret = json_encode($data, JSON_PRETTY_PRINT);
        }

        return $ret;
    }

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     *
     * @throws SynfulException
     */
    public function deserialize(string $data) : array
    {
        if (! sf_is_json($data)) {
            throw new SynfulException(400, 1020);
        }

        return json_decode($data, true);
    }
}
