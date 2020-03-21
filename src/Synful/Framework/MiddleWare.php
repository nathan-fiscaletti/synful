<?php

namespace Synful\Framework;

interface Middleware
{
    /**
     * Perform the specified action on the request before
     * passing it to the Route.
     *
     * @param Request $request
     * @param Route   $route
     */
    public function before(Request $request, Route $route);

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param Response $response
     */
    public function after(Response $response);
}
