<?php

/*
 |------------------------------------------------------------------------------
 | Synful Functions
 |------------------------------------------------------------------------------
 |
 | This set of functions is used to abstract the
 | calls to the Primary Synful functions.
 */

use Synful\Ansi\StringBuilder;
use Synful\Framework\Response;
use Synful\Framework\SynfulException;
use Synful\Synful;

if (! function_exists('sf_init')) {
    /**
     * Initialize the Synful API instance.
     */
    function sf_init()
    {
        Synful::initialize();
    }
}

if (! function_exists('sf_conf')) {
    /**
     * Retreive a config value from the Configuration object.
     *
     * @param  string $key
     * @return mixed
     */
    function sf_conf($key)
    {
        return Synful::$config->get($key);
    }
}

if (! function_exists('sf_is_json')) {
    /**
     * Check if a string is valid JSON.
     *
     * @param  string
     * @return bool
     */
    function sf_is_json($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}

if (! function_exists('sf_json_decode')) {
    /**
     * Takes a JSON encoded string removes any comments that might be in it and
     * converts it into a PHP variable.
     *
     * @param $json
     * @param $assoc
     *
     * @return array
     */
    function sf_json_decode($json, $assoc)
    {
        $json = preg_replace(
            "#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#",
            '',
            $json
        );

        return json_decode($json, $assoc);
    }
}

if (! function_exists('sf_response')) {
    /**
     * Generate a response.
     *
     * @param  int         $code
     * @param  array       $response
     *
     * @throws SynfulException
     * @return Response
     */
    function sf_response(int $code = 200, $response = null)
    {
        if ($response != null && ! is_array($response)) {
            throw new SynfulException(500, 1016);
        }

        if ($response == null) {
            $response = [];
        }

        return new Response([
            'code' => $code,
            'response' => $response,
        ]);
    }
}

if (! function_exists('sf_headers')) {
    /**
     * Retrieve all headers from the System.
     *
     * @return array
     */
    function sf_headers()
    {
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (substr($k, 0, 5) == 'HTTP_') {
                $headers[
                    str_replace(
                        '-',
                        '_',
                        str_replace(
                            ' ',
                            '_',
                            strtolower(
                                substr($k, 5)
                            )
                        )
                    )
                ] = $v;
            }
        }

        return $headers;
    }
}

if (! function_exists('sf_color')) {
    /**
     * Colors a string and returns it.
     *
     * @param  string $string
     * @param  int    $color
     *
     * @return \Ansi\StringBuilder
     */
    function sf_color($string, int $color)
    {
        return (new StringBuilder())->color16($string, $color);
    }
}
