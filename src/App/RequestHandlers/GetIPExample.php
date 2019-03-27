<?php

namespace App\RequestHandlers;

use Synful\Framework\Request;
use Synful\Framework\RequestHandler;

/**
 * Class used to demonstrate retrieving an IP address from a Request.
 */
class GetIPExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/getip';

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function get(Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }
}
