<?php

namespace Synful\Util\Standalone;

use Synful\Synful;
use Synful\Util\Framework\Object;

/**
 * Class used for handling multithread client socket communication.
 */
class ClientHandleMultiThread extends Thread
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
    public function run()
    {
        $input = socket_read($this->client_socket, 2024);

        sf_info(
            'Client REQ ('.$this->ip.':'.$this->port.'): '.$input
        );

        if (sf_conf('security.use_encryption')) {
            $response = Synful::handleRequest(sf_decrypt($input), $this->ip);
            socket_write(
                $this->client_socket,
                sf_encrypt(json_encode($response)),
                strlen(sf_encrypt(json_encode($response)))
            );
        } else {
            $response = Synful::handleRequest($input, $this->ip);
            socket_write($this->client_socket, json_encode($response), strlen(json_encode($response)));
        }

        sf_info(
            'Server RES ('.$this->ip.':'.$this->port.'): '.json_encode($response)
        );

        socket_close($this->client_socket);
        unset($this);
    }
}
