<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * Class used to demonstrate request types.
 */
class RequestTypeExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * You can also add fields to the endpoint
     * by encapsulating them with `{field_name}`.
     *
     * You can access these fields using
     * `$request->field('field_name')`.
     *
     * @var string
     */
    public $endpoint = 'example/requesttype';

    /**
     * Handles a POST request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function post(Request $request)
    {
        return sf_response(
            200,
            [
                'message' => 'Received POST request!',
            ]
        );
    }

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        return sf_response(
            200,
            [
                'message' => 'Received GET request!',
            ]
        );
    }

    /**
     * Handles a PUT request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function put(Request $request)
    {
        return sf_response(
            200,
            [
                'message' => 'Received PUT request!',
            ]
        );
    }

    /**
     * Handles a DELETE request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function delete(Request $request)
    {
        return sf_response(
            200,
            [
                'message' => 'Received DELETE request!',
            ]
        );
    }
}
