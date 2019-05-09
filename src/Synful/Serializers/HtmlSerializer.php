<?php

namespace Synful\Serializers;

use Synful\Framework\Serializer;

class HtmlSerializer implements Serializer
{
    /**
     * The content type for the serialized data.
     *
     * @var string
     */
    public $content_type = 'text/html';

    /**
     * Serialize the data to be sent back to the client.
     *
     * @param  array  $data
     * @return string
     */
    public function serialize(array $data) : string
    {
        return isset($data['html']) ? $data['html'] : '';
    }

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     */
    public function deserialize(string $data) : array
    {
        return ['html' => $data];
    }
}
