<?php

namespace Synful\Util\Framework;

/**
 * Interface used to handle Request Handlers.
 */
interface RequestHandler
{
    /**
     * Function for handling request and returning a response.
     *
     * @param Request $request
     */
    public function handleRequest(Request $request);
}
