<?php

namespace Synful\Middleware;

use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\Middleware;

/**
 * Custom Middleware implementation.
 */
class Cors implements Middleware
{
    /**
     * The property key used to associate the
     * Middleware Route properties.
     * 
     * @var string
     */
    public $key = 'cors';

    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Framework\Request $request
     */
    public function before(Request $request)
    {
        /* Not implemented */
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Framwork\Response $response
     */
    public function after(Response $response)
    {
        $domains = $response->request->route->middlewareProperty($this, 'domains');
        if (is_array($domains)) {
            if (in_array('*', $domains)) {
                header('Access-Control-Allow-Origin: *');
            } else if (in_array($_SERVER['HTTP_ORIGIN'], $domains)) {
                header('Access-Control-Allow-Origin: '.$domain);
            }
        }
    }
}
