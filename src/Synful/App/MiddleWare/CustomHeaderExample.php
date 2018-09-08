<?php

namespace Synful\App\MiddleWare;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\Response;
use Synful\Util\Framework\MiddleWare;
use Synful\Util\Framework\RequestHandler;

/**
 * Custom MiddleWare implementation.
 */
class CustomHeaderExample implements MiddleWare
{
    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Util\Framework\Request        $request
     * @param  \Synful\Util\Framework\RequestHandler $handler
     * @return bool
     */
    public function before(Request $request, RequestHandler $handler)
    {
        $request->setHeader('Custom-Header', 'Custom Input Value');
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Util\Framwork\Response $response
     */
    public function after(Response $response)
    {
        $response->setHeader('Custom-Header', 'Custom Output Value');
    }
}
