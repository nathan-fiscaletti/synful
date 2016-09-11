<?php

namespace Synful\Standalone;

use Synful\Synful;
use Synful\IO\IOFunctions;
use Synful\IO\LogLevel;

/**
 * Class used to handle Standalone instance of Synful.
 */
class Standalone
{
    /**
     * Runs Synful in Standalone Mode.
     */
    final public function initialize()
    {
        set_time_limit(0);

        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        $bind = socket_bind($sock, Synful::$config->get('system.ip'), Synful::$config->get('system.port'));

        if ($bind) {
            IOFunctions::out(
                LogLevel::INFO,
                'Listening on '.Synful::$config->get('system.ip').':'.Synful::$config->get('system.port')
            );
        } else {
            exit(1);
        }

        socket_listen($sock);

        while (true) {
            $client = socket_accept($sock);
            $client_ip = '';
            $client_port = 0;
            socket_getpeername($client, $client_ip, $client_port);
            if (Synful::$config->get('system.multithread')) {
                (new \Synful\Standalone\ClientHandleMultiThread(
                    [
                        'ip' => $client_ip,
                        'port' => $client_port,
                        'client_socket' => $client,
                    ]
                ))->start();
            } else {
                (new \Synful\Standalone\ClientHandle(
                    [
                        'ip' => $client_ip,
                        'port' => $client_port,
                        'client_socket' => $client,
                    ]
                ))->start();
            }
        }
    }
}
