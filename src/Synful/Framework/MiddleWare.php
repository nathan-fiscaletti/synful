<?php

namespace Synful\Framework;

interface Middleware
{
    /**
     * Perform the specified action on the request before
     * passing it to the Route.
     *
     * @param  \Synful\Framework\Request        $request
     */
    public function before(Request $request);

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Framwork\Response $response
     */
    public function after(Response $response);
}
