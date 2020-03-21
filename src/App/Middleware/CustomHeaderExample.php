<?php

namespace App\Middleware;

use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\Middleware;
use Synful\Framework\Route;

/**
 * Custom MiddleWare implementation.
 */
class CustomHeaderExample implements Middleware
{
    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param Request $request
     * @param Route $route
     */
    public function before(Request $request, Route $route)
    {
        $request->setHeader('Custom-Header', 'Custom Input Value');
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param Response $response
     */
    public function after(Response $response)
    {
        $response->setHeader('Custom-Header', 'Custom Output Value');
    }
}
