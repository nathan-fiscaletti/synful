<?php

namespace Synful\Serializers;

use Synful\Framework\Serializer;

class URLSerializer implements Serializer
{
    /**
     * The content type for the serialized data.
     *
     * @var string
     */
    public $mime_type = 'application/x-www-form-urlencoded';

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
