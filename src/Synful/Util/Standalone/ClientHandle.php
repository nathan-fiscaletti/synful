<?php

namespace Synful\Util\Standalone;

use Synful\Synful;
use Synful\Util\Framework\Object;

/**
 * Class used for handling client socket communication.
 */
class ClientHandle
{
    use Object;

    /**
     * The socket used for the client connection.
     *
     * @var socket
     */
    private $client_socket;

    /**
     * The ip address of the connecting client.
     *
     * @var string
     */
    private $ip;

    /**
     * The port of the connecting client.
     *
     * @var int
     */
    private $port;

    /**
     * Initializes the new handler for the request.
     */
    public function start()
    {
        $input = socket_read($this->client_socket, 4096);

        sf_info(
            'Client REQ ('.$this->ip.':'.$this->port.')'
        );

        if (empty($input)) {
            $response = (new SynfulException(null, 400, 1013))->response;
        } else {
            $response = Synful::handleRequest($input, $this->ip);

            if (! sf_is_json($input)) {
                if (sf_is_json(sf_decrypt($input))) {
                    $response = Synful::handleRequest(
                        Synful::$crypto->decrypt($input),
                        Synful::getClientIP(),
                        true
                    );
                    $response->encrypt_response = true;
                }
            }
        }

        sf_info(
            'Client RES ('.$this->ip.':'.$this->port.')'
        );

        socket_write($this->client_socket, $response->serialize(), strlen($response->serialize()));
        socket_close($this->client_socket);
        unset($this);
    }
}
