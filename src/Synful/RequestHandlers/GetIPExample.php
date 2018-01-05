<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class GetIPExample implements RequestHandler
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
     * Function for handling request and returning a response.
     *
     * @param Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function handleRequest(Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }
}
