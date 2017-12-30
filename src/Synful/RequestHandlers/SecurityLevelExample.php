<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Response;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class SecurityLevelExample implements RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`
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
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     */
    public function handleRequest(Response &$response)
    {
        $request_data = &$response->request;

        $response->setResponse(
            'message',
            'This API key has a security level equal to or greater than 4.'
        );
    }
}
