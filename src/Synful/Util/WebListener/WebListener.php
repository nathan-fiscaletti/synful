<?php

namespace Synful\Util\WebListener;

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
            if (sf_conf('security.use_encryption')) {
                sf_respond(sf_encrypt(json_encode($response)));
            } else {
                if (sf_conf('system.pretty_responses') || isset($_GET['pretty'])) {
                    sf_respond(json_encode($response, JSON_PRETTY_PRINT));
                } else {
                    sf_respond(json_encode($response));
                }
            }
        } else {
            if (sf_conf('security.use_encryption')) {
                $response = self::handleRequest(
                    self::$crypto->decrypt($_POST['request']),
                    self::getClientIP()
                );
                sf_respond(sf_encrypt(json_encode($response)));
            } else {
                $response = self::handleRequest($_POST['request'], self::getClientIP());
                if (sf_conf('system.pretty_responses') || (isset($_GET['pretty'])
                    && Synful::$config->get('system.allow_pretty_responses_on_get'))) {
                    sf_respond(json_encode($response, JSON_PRETTY_PRINT));
                } else {
                    sf_respond(json_encode($response));
                }
            }
        }
    }
}
