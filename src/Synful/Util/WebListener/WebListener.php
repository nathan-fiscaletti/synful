<?php

namespace Synful\Util\WebListener;

use Synful\Synful;
use Synful\Util\Framework\SynfulException;

/**
 * Class used to listen on web sockets.
 */
class WebListener
{
    /**
     * Runs the API thread on the local web server
     * and outputs it's response in JSON format.
     */
    final public function initialize()
    {
        if (empty($_POST['request'])) {
            $response = (new SynfulException(null, 400, 1013))->response;
            sf_respond($response->code, $response->serialize());
        } else {
            $response = Synful::handleRequest(
                $_POST['request'],
                Synful::getClientIP()
            );

            if (! sf_is_json($_POST['request'])) {
                if (sf_is_json(sf_decrypt($_POST['request']))) {
                    $response = Synful::handleRequest(
                        Synful::$crypto->decrypt($_POST['request']),
                        Synful::getClientIP(),
                        true
                    );
                    $response->encrypt_response = true;
                }
            }

            sf_respond($response->code, $response->serialize());
        }
    }
}
