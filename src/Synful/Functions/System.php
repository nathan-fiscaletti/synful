<?php

/*
 |------------------------------------------------------------------------------
 | Synful Functions
 |------------------------------------------------------------------------------
 |
 | This set of functions is used to abstract the
 | calls to the Primary Synful functions.
 */

if (! function_exists('sf_init')) {
    /**
     * Initialize the Synful API instance.
     *
     * @return mixed
     */
    function sf_init()
    {
        return \Synful\Synful::initialize();
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
        return \Synful\Synful::$config->get($key);
    }
}

if (! function_exists('sf_is_json')) {
    /**
     * Check if a string is valid JSON.
     *
     * @param  string
     * @return boo
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
     * @return \Synful\Framework\Response
     */
    function sf_response(int $code = 200, $response = null)
    {
        if ($response != null && ! is_array($response)) {
            throw new \Synful\Framework\SynfulException(500, 1016);
        }

        if ($response == null) {
            $response = [];
        }

        return new \Synful\Framework\Response([
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
                $headers[str_replace('-', '_', str_replace(' ', '_', strtolower(substr($k, 5))))] = $v;
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
     */
    function sf_color($string, int $color)
    {
        return (new \Synful\Ansi\StringBuilder())->color16($string, $color);
    }
}