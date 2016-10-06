<?php

/*
 |------------------------------------------------------------------------------
 | Encryption Functions
 |------------------------------------------------------------------------------
 |
 | This set of functions is used to abstract the calls to the Encryption class.
 */

if (! function_exists('sf_encrypt')) {

    /**
     * Handle encrypting data.
     *
     * @param  string $data
     * @return string
     */
    function sf_encrypt($data)
    {
        return \Synful\Synful::$crypto->encrypt($data);
    }

}

if (! function_exists('sf_decrypt')) {

    /**
     * Handle decrypting data.
     *
     * @param  string $data
     * @return string
     */
    function sf_decrypt($data)
    {
        return \Synful\Synful::$crypto->decrypt($data);
    }

}
