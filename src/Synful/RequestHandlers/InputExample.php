<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class InputExample implements RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/input';

    /**
     * Function for handling request and returning a response.
     *
     * @param Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function handleRequest(Request $request)
    {
        // Read input from the data passed in the request.
        $name = $request->input('name');

        if ($name == null) {
            return sf_response(
                500,
                [
                    'error' => 'Missing name parameter.',
                ]
            );
        } else {
            return [
                'your_name_is' => $name,
            ];
        }
    }
}
