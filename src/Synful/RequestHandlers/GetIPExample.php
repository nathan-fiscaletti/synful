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
     * Construct the request handler as a public request handler (wont require an API key).
     * If you disable allow_public_requests in Security.php,
     * this will not matter and an API key will always be required.
     */
    public function __construct()
    {
        $this->is_public = true;
    }

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
