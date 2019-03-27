<?php

namespace Synful\Serializers;

use Synful\Framework\Serializer;

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
        $fh = fopen('php://temp', 'rw');
        fputcsv($fh, array_keys(current($data)));
        foreach ($data as $row) {
            fputcsv($fh, $row);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $csv;
    }

    /**
     * Deserialize the data coming from the request.
     *
     * @param  string $data
     * @return array
     */
    public function deserialize(string $data) : array
    {
        $csv = str_getcsv($data, "\n");
        foreach ($csv as &$row) {
            $row = str_getcsv($row);
        }

        array_walk($csv, function (&$value) use ($csv) {
            $value = array_combine($csv[0], $value);
        });

        array_shift($csv);

        return $csv;
    }
}
