<?php

namespace App\RequestHandlers;

use Synful\Framework\Request;
use Synful\Framework\RequestHandler;

/**
 * Class used to demonstrate data input.
 */
class InputExample extends RequestHandler
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
     * Handles a POST request type.
     *
     * @param  \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function post(Request $request)
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
