<?php

namespace Synful\RequestHandlers\Interfaces;

use Synful\Response;

/**
 * Interface used to handle Request Handlers.
 */
interface RequestHandler
{
    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $data
     * @param  bool  $is_master_request
     */
    public function handleRequest(Response &$data, $is_master_request = false);
}
