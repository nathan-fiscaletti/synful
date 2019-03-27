<?php

namespace Synful\Framework;

interface MiddleWare
{
    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Framework\Request        $request
     * @param  \Synful\Framework\RequestHandler $handler
     * @return bool
     */
    public function before(Request $request, RequestHandler $handler);

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Framwork\Response $response
     */
    public function after(Response $response);
}
