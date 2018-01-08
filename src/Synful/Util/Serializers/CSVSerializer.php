<?php

namespace Synful\Util\Serializers;

use Synful\Util\Framework\Serializer;

/**
 * Note: This class is only for demonstrational purposes.
 *       It is not advised that you use it in production.
 */
class CSVSerializer implements Serializer
{
    /**
     * The content type for the serialized data.
     *
     * @var string
     */
    public $content_type = 'text/csv';

    /**
     * Serialize the data to be sent back to the client.
     *
     * @param  array  $data
     * @return string
     */
    public function serialize(array $data) : string
    {
        $ret = '';

        foreach ($data as $field) {
            $ret .= ((($ret == '') ? '' : ',').$field);
        }

        return $ret;
    }

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     */
    public function deserialize(string $data) : array
    {
        return str_getcsv($data);
    }
}
