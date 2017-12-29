<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Response;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\DataManagement\Models\APIKey;

/**
 * Class used to demonstrate private request handlers.
 */
class PrivateHandlerExample implements RequestHandler
{
    /**
     * Assign an array of API Keys to the 'white_list_keys' property to make
     * this handler only allow connections using those API Keys.
     */
    public function __construct()
    {
        $this->security_level = 4;
        $this->white_list_keys = [
            'john@acme.com',
        ];
    }

    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     */
    public function handleRequest(Response &$response)
    {
        $request_data = &$response->request;

        $api_key = APIKey::getKey($response->requesting_email);
        $response->code = 200;
        $response->setResponse('user_information', [
            'name' => $api_key->name,
            'email' => $api_key->email,
            'enabled' => $api_key->enabled,
            'whitelist_only' => $api_key->whitelist_only,
            'firewall' => $api_key->ip_firewall,
        ]);
    }
}
