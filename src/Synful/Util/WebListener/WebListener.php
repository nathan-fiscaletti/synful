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
        $json = file_get_contents('php://input');
        if (empty($json)) {
            $response = (new SynfulException(null, 400, 1013))->response;
            sf_respond($response->code, $response->serialize());
        } else {
            $response = Synful::handleRequest(
                $json,
                Synful::getClientIP()
            );

            if (! sf_is_json($json)) {
                if (sf_is_json(sf_decrypt($json))) {
                    $response = Synful::handleRequest(
                        Synful::$crypto->decrypt($json),
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
