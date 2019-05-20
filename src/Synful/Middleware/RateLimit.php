<?php

namespace Synful\Middleware;

use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\Middleware;
use Synful\Framework\SynfulException;
use Synful\Middleware\APIKeyValidation;
use Synful\Framework\RateLimit as Limit;

/**
 * Custom Middleware implementation.
 */
class RateLimit implements Middleware
{
    /**
     * The property key used to associate the
     * Middleware Route properties.
     * 
     * @var string
     */
    public $key = 'rate_limit';

    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Framework\Request $request
     */
    public function before(Request $request)
    {
        $method = $request->route->middlewareProperty($this, 'method');
        if ($method != 'api_key' && $method != 'ip') {
            throw new SynfulException(500, 1033);
        }

        if (
            $request->route->middlewareProperty($this, 'method') == 'api_key'
        ) {
            if (
                ! $request->route->hasMiddleware(
                    APIKeyValidation::class
                )
            ) {
                throw new SynfulException(500, 1034);
            }
        }

        // Generate the Key for the Rate Limit.
        $key = $request->ip;
        if ($method == 'api_key') {
            $keyHeader = $request->header('Synful-Auth');
            if (is_null($keyHeader)) {
                throw new SynfulException(500, 1035);
            }
            $key = $request->ip.'.'.$keyHeader;
        }

        $requests = $request->route->middlewareProperty($this, 'requests');
        $seconds = $request->route->middlewareProperty($this, 'seconds');

        $limit = new Limit(
            $request->route->path,
            $requests,
            $seconds   
        );

        if ($limit->isLimited($key)) {
            throw new SynfulException(429, 1036);
        }
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Framwork\Response $response
     */
    public function after(Response $response)
    {
        // Not implemented
    }
}
