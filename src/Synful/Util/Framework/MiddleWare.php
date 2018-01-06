<?php

namespace Synful\Util\Framework;

interface MiddleWare
{
    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Util\Framework\Request        $request
     * @param  \Synful\Util\Framework\RequestHandler $handler
     * @return bool
     */
    public function before(Request $request, RequestHandler $handler);

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param  Response $response
     */
    public function after(Response $response);
}
