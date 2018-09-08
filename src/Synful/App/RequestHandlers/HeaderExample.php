<?php

namespace Synful\App\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * Class used to demonstrate retrieving an IP address from a Request.
 */
class HeaderExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/header';

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        $response = sf_response(200, ['success' => true]);
        $response->setHeader('Test', 'It Worked!');

        return $response;
    }
}
