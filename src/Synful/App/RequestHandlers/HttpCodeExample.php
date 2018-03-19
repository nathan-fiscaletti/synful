<?php

namespace Synful\App\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
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
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
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
