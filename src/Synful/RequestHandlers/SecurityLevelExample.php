<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class SecurityLevelExample implements RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     */
    public $endpoint = 'example/secure';

    /**
     * Set the security level for the RequestHandler.
     * Only API keys with this security level or
     * higher can access this RequestHandler.
     *
     * @var int
     */
    public $security_level = 4;

    /**
     * Function for handling request and returning a response.
     *
     * @param Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function handleRequest(Request $request)
    {
        return [
            'message' => 'This API key has a security level equal to or greater than 4.',
        ];
    }
}
