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
            $response = (new SynfulException(500, 1002))->response;
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
            $response = (new SynfulException(404, 1001))->response;
            sf_respond($response->code, $response->serialize());
            exit;
        }

        if (empty($json)) {
            $json = '{}';
        }

        if (! sf_is_json($json)) {
            $response = (new SynfulException(400, 1013))->response;
            sf_respond($response->code, $response->serialize());
            exit;
        }

        $response = Synful::handleRequest(
            $selected_handler,
            $json,
            Synful::getClientIP()
        );

        sf_respond($response->code, $response->serialize());
    }
}
