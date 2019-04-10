<?php

namespace App\MiddleWare;

use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\MiddleWare;
use Synful\Framework\Route;

/**
 * Custom MiddleWare implementation.
 */
class CustomHeaderExample implements MiddleWare
{
    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Framework\Request $request
     * @param  \Synful\Framework\Route   $route
     * @return bool
     */
    public function before(Request $request, Route $route)
    {
        $request->setHeader('Custom-Header', 'Custom Input Value');
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Framwork\Response $response
     */
    public function after(Response $response)
    {
        $response->setHeader('Custom-Header', 'Custom Output Value');
    }
}
