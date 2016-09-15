<?php

namespace Synful\RequestHandlers;

use Synful\RequestHandlers\Interfaces\RequestHandler;
use Synful\Response;

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
     * @param  Response $data
     * @param  bool  $is_master_request
     */
    public function handleRequest(Response &$data, $is_master_request = false)
    {
        $request_data = &$data->request;

        // Set the response code
        $data->code = 200;

        // Add the 'ip' field to the request data using the client's requesting_ip
        $data->setResponse('ip', $data->requesting_ip);
    }
}
