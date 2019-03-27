<?php

namespace App\RequestHandlers;

use Synful\Framework\Request;
use Synful\Data\Models\APIKey;
use Synful\Framework\RequestHandler;
use Synful\MiddleWare\APIKeyValidation;

/**
 * Class used to demonstrate private request handlers.
 */
class PrivateHandlerExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/private';

    /**
     * Implement the APIKeyValidation middleware
     * in order to require an API key to access
     * this RequestHandler.
     *
     * @var array
     */
    public $middleware = [
        APIKeyValidation::class,
    ];

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function get(Request $request)
    {
        $api_key = APIKey::getApiKey($request->auth);

        return [
            'user-information' => [
                'name' => $api_key->name,
                'auth' => $api_key->auth,
                'enabled' => $api_key->enabled,
                'whitelist_only' => $api_key->whitelist_only,
                'firewall' => $api_key->ip_firewall,
            ],
        ];
    }
}
