<?php

namespace Synful\Util\Serializers;

use Synful\Util\Framework\Serializer;
use Synful\Util\Framework\SynfulException;

class URLSerializer implements Serializer
{
    /**
     * The content type for the serialized data.
     *
     * @var string
     */
    public $content_type = 'application/x-www-form-urlencoded';

    /**
     * Serialize the data to be sent back to the client.
     *
     * @param  array  $data
     * @return string
     */
    public function serialize(array $data) : string
    {
        return http_build_query($data);
    }

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     */
    public function deserialize(string $data) : array
    {
        $out = [];
        parse_str($data, $out);
        return $out;
    }
}
