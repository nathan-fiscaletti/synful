<?php

namespace App\RequestHandlers;

use Synful\Framework\Request;
use Synful\Framework\RequestHandler;

/**
 * Class used to demonstrate HTTP codes.
 */
class HttpCodeExample extends RequestHandler
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
     * Handles a GET request type.
     *
     * @param  \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function get(Request $request)
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
