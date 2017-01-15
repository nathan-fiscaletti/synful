<?php

namespace Synful\RequestHandlers;

use Synful\RequestHandlers\Interfaces\RequestHandler;
use Synful\Util\Framework\Response;

/**
 * New Request Handler Class.
 */
class EncryptedOnlyHandlerExample implements RequestHandler
{
    /**
     * To make this a public request handler, change is_public to 'true'.
     * Note: When set to 'true', this request handler
     * will not require an API Key.
     *
     * To make this a private request handler assign an array of API Keys to the
     * 'white_list_keys' property which will make this handler only allow
     * connections using those API Keys.
     *
     * To make this a standard request handler, simply remove the constructor.
     * The request handler will only be accessible with API keys, but any
     * API key will be able to access it.
     */
    public function __construct()
    {
        $this->is_public = false;
        $this->encrypted_only = true;
        $this->white_list_keys = [
            'john@acme.com',
        ];
    }

    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     * @param  bool     $is_master_request
     */
    public function handleRequest(Response &$response, $is_master_request = false)
    {
        $request_data = &$response->request;

        // Respond with a message telling the client that the
        // encrypted request was received.
        $response->code = 200;
        $response->setResponse('Encrypted Request Received', 'true');
    }
}
