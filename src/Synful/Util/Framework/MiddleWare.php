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
    public function action(Request $request, RequestHandler $handler);
}
