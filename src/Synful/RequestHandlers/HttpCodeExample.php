<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class HttpCodeExample implements RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/httpcode';

    /**
     * Function for handling request and returning a response.
     *
     * @param Request $request
     */
    public function handleRequest(Request $request)
    {
        // Return a 401 error code.
        return sf_response(
            401
        );

        // Alternately, you can return a 401 with a response body.
        return sf_response(
            401,
            [
                'error' => 'You are not authorized to be here.',
            ]
        );
    }
}
