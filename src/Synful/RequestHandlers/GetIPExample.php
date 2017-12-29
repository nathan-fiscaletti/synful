<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Response;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class GetIPExample implements RequestHandler
{
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
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     */
    public function handleRequest(Response &$response)
    {
        $request_data = &$response->request;

        // Set the response code
        $response->code = 200;

        // Add the 'ip' field to the request data using the client's requesting_ip
        $response->setResponse('ip', $response->requesting_ip);
    }
}
