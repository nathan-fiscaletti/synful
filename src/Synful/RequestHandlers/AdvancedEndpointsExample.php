<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class AdvancedEndpointsExample implements RequestHandler
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
     * Function for handling request and returning a response.
     *
     * @param Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function handleRequest(Request $request)
    {
        return [
            'message' => 'You selected ID: '.$request->field('id'),
        ];
    }
}
