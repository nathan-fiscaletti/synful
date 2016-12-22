<?php

namespace Synful\RequestHandlers\Interfaces;

use Synful\Util\Framework\Response;

/**
 * Interface used to handle Request Handlers.
 */
interface RequestHandler
{
    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     * @param  bool  $is_master_request
     */
    public function handleRequest(Response &$response, $is_master_request = false);
}
