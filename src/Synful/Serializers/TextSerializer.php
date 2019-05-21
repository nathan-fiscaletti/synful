<?php

namespace Synful\Serializers;

use Synful\Framework\Serializer;

class TextSerializer implements Serializer
{
    /**
     * The content type for the serialized data.
     *
     * @var string
     */
    public $mime_type = 'text/plain';

    /**
     * Serialize the data to be sent back to the client.
     *
     * @param  array  $data
     * @return string
     */
    public function serialize(array $data) : string
    {
        return isset($data['text']) ? $data['text'] : '';
    }

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     */
    public function deserialize(string $data) : array
    {
        return ['text' => $data];
    }
}
