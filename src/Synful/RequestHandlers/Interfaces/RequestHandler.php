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
     */
    public function handleRequest(Response &$response);
}
