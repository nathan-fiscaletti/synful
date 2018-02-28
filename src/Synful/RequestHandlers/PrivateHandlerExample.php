<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\MiddleWare\APIKeyValidation;
use Synful\Util\DataManagement\Models\APIKey;

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
     * this RequestHandler. This is also used to
     * parse the white_list_keys property.
     *
     * @var array
     */
    public $middleware = [
        APIKeyValidation::class,
    ];

    /**
     * Assign an array of API Keys to the 'white_list_keys' property to make
     * this handler only allow connections using those API Keys.
     *
     * Note: Must implement the APIKeyValidation middleware.
     *
     * @var array
     */
    public $white_list_keys = [
        'n@synful.io',
    ];

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        $api_key = APIKey::getKey($request->email);

        return [
            'user-information' => [
                'name' => $api_key->name,
                'email' => $api_key->email,
                'enabled' => $api_key->enabled,
                'whitelist_only' => $api_key->whitelist_only,
                'firewall' => $api_key->ip_firewall,
            ],
        ];
    }
}
