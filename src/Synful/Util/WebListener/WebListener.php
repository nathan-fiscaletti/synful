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
        if (! isset($_GET['_synful_ep_'])) {
            $response = (new SynfulException(null, 500, 1002))->response;
            sf_respond($response->code, $response->serialize());
            exit;
        }

        $json = file_get_contents('php://input');
        $endpoint = $_GET['_synful_ep_'];
        $found_endpoint = false;
        $selected_handler = null;

        foreach (Synful::$request_handlers as $handler_name => $handler) {
            if (property_exists($handler, 'endpoint')) {
                if ($handler->endpoint == $endpoint) {
                    $found_endpoint = true;
                    $selected_handler = $handler_name;
                    break;
                }
            }
        }

        if (! $found_endpoint) {
            $response = (new SynfulException(null, 404, 1001))->response;
            sf_respond($response->code, $response->serialize());
            exit;
        }

        if (empty($json)) {
            $json = '{}';
        }

        $response = Synful::handleRequest(
            $selected_handler,
            $json,
            Synful::getClientIP()
        );

        if (! sf_is_json($json)) {
            if (sf_is_json(sf_decrypt($json))) {
                $response = Synful::handleRequest(
                    $selected_handler,
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
