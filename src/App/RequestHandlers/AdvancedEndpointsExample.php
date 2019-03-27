<?php

namespace App\RequestHandlers;

use Synful\Framework\Request;
use Synful\Framework\RequestHandler;

/**
 * Class used to demonstrate URL fields.
 */
class AdvancedEndpointsExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * You can also add fields to the endpoint,
     * In this example, we add the `id` field.
     *
     * You can access these fields using
     * `$request->field('id')`.
     *
     * @var string
     */
    public $endpoint = 'example/endpoint/{id}';

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function get(Request $request)
    {
        return [
            'message' => 'You selected ID: '.$request->field('id'),
        ];
    }
}
