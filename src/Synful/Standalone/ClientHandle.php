<?php

namespace Synful\Standalone;

use Synful\Synful;
use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;
use Synful\Util\Object;
use Synful\Util\Encryption;

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
        $input = socket_read($this->client_socket, 2024);

        IOFunctions::out(
            LogLevel::INFO,
            'Client REQ ('.$this->ip.':'.$this->port.')'
        );

        if (Synful::$config->get('security.use_encryption')) {
            $response = Synful::$controller->handleRequest(Synful::$crypto->decrypt($input), $this->ip);
            socket_write(
                $this->client_socket,
                Synful::$crypto->encrypt(json_encode($response)),
                strlen(json_encode($response))
            );
        } else {
            $response = Synful::$controller->handleRequest($input, $this->ip);
            socket_write($this->client_socket, json_encode($response), strlen(json_encode($response)));
        }

        IOFunctions::out(
            LogLevel::INFO,
            'Server RES ('.$this->ip.':'.$this->port.') : '.json_encode($response)
        );

        socket_close($this->client_socket);
        unset($this);
    }
}
