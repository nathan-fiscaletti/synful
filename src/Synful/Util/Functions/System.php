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
     * Initialize the Synful API instance using either Standalone Mode
     * or Local Web Server.
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
