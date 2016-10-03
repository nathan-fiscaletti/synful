<?php

namespace Synful\RequestHandlers;

use Synful\RequestHandlers\Interfaces\RequestHandler;
use Synful\Response;
use Synful\DataManagement\Models\APIKey;

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
        $this->white_list_keys = [
            'john@acme.com',
        ];
    }

    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $data
     * @param  bool  $is_master_request
     */
    public function handleRequest(Response &$data, $is_master_request = false)
    {
        $request_data = &$data->request;

        $api_key = APIKey::getKey($data->requesting_email);
        $data->code = 200;
        $data->setResponse('user_information', [
            'name' => $api_key->name,
            'email' => $api_key->email,
            'is_master' => $api_key->is_master,
            'enabled' => $api_key->enabled,
            'whitelist_only' => $api_key->whitelist_only,
            'firewall' => $api_key->ip_firewall,
        ]);
    }
}
