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
     */
    public $endpoint = 'example/getip';

    /**
     * Set the `is_public` property to make this  a public request handler.
     * When a request handler is set to public, it won't require an API key.
     *
     * Note If you disable allow_public_requests in Security.php,
     * this will not matter and an API key will always be required.
     *
     * Note: If you are using only public RequestHandlers, you will not
     * need any database configuration to run Synful.
     *
     * @var bool
     */
    public $is_public = true;

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
