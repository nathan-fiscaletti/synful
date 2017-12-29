<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Response;
use Synful\Util\Framework\RequestHandler;

/**
 * New Request Handler Class.
 */
class EncryptedOnlyHandlerExample implements RequestHandler
{
    /**
     * To make this an encrypted only RequestHandler
     * set the `encrypted_only` property of the class.
     *
     * @var bool
     */
    public $encrypted_only = true;

    /**
     * Function for handling request and returning data as a Response object.
     *
     * @param  Response $response
     */
    public function handleRequest(Response &$response)
    {
        $request_data = &$response->request;

        // Respond with a message telling the client that the
        // encrypted request was received.
        $response->code = 200;
        $response->setResponse('Encrypted Request Received', 'true');
    }
}
