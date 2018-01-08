<?php

namespace Synful\Util\Framework;

interface Serializer
{
    /**
     * Serialize the data to be sent back to the client.
     *
     * @param  array  $data
     * @return string
     */
    public function serialize(array $data) : string;

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     */
    public function deserialize(string $data) : array;
}