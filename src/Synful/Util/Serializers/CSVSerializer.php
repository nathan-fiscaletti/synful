<?php

namespace Synful\Util\Serializers;

use Synful\Util\Framework\Serializer;
use Synful\Util\Framework\SynfulException;

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
        $escapedKeys = array_keys($data[0]);
        array_walk($escapedKeys, function(&$value, $key) {
            if (strpos($value, ' ') !== false) {
                $value = '"'.$value.'"';
            }
        });
        $csv .= implode(',', $escapedKeys).PHP_EOL;

        foreach ($data as $row) {
            array_walk($row, function(&$value, $key) {
                if (strpos($value, ' ') !== false) {
                    $value = '"'.$value.'"';
                }
            });

            $csv .= implode(',', $row).PHP_EOL;
        }
        
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

        foreach($csv as &$row) {
            $row = str_getcsv($row);
        }

        array_walk($csv, function(&$value) use ($csv) {
            $value = array_combine($csv[0], $value);
        });

        array_shift($csv);

        return $data;
    }
}
