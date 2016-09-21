<?php

namespace Synful\Util;

/**
 * Class used for encrypting requests and responses.
 */
class Encryption
{
    /**
     * The salt bytes after the salt has been shifted.
     *
     * @var array
     */
    private $salt_data;

    /**
     * Construct the new Encryption object.
     *
     * @param string $salt
     * @param int    $strength
     */
    public function __construct($salt, $strength)
    {
        $this->salt_data = [];
        $salt_bytes = $this->toAscii($salt);
        for ($i = 0; $i < count($salt_bytes); $i++) {
            $val = (int) ($salt_bytes[$i] + (125 - $strength));
            $val = ($val > 127) ? 127 : $val;
            $val = ($val < -127) ? -127 : $val;
            $this->salt_data[$i] = $val;
        }
    }

    /**
     * Encrypts data based on the defined encryption key.
     *
     * @param  string $str
     * @return string
     */
    public function encrypt($str)
    {
        $c = 0;
        $x = [];
        $z = $this->toAscii($str);
        for ($i = 0; $i < count($z); $i++) {
            $x[$i] = ($z[$i] + $this->salt_data[$c]);
            $c = ($c == count($this->salt_data) - 1) ? 0 : $c + 1;
        }

        return implode(' ', $x);
    }

    /**
     * Decrypts data based on the defined encryption key.
     *
     * @param  string  $data
     * @return string
     */
    public function decrypt($data)
    {
        $c = 0;
        $x = [];
        $z = explode(' ', $data);
        for ($i = 0; $i < count($z); $i++) {
            $x[$i] = ($z[$i] - $this->salt_data[$c]);
            $c = ($c == count($this->salt_data) - 1) ? 0 : $c + 1;
        }

        return $this->fromAscii($x);
    }

    /**
     * Convert a string to an array of bytes.
     *
     * @param  string $string
     * @return array
     */
    public function toAscii($string)
    {
        $ret = [];
        foreach (str_split($string) as $chr) {
            $ret[] = ord($chr);
        }

        return $ret;
    }

    /**
     * Convert an array of bytes to a string.
     *
     * @param  array  $arr
     * @return string
     */
    public function fromAscii($arr)
    {
        return implode(array_map('chr', $arr));
    }
}
