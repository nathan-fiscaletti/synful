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

        $input = file_get_contents('php://input');
        $endpoint = $_GET['_synful_ep_'];
        $found_endpoint = false;
        $selected_handler = null;
        $fields = [];

        foreach (Synful::$request_handlers as $handler_name => $handler) {
            if (property_exists($handler, 'endpoint')) {
                // Separate the parameters from the endpoint.
                $handler_endpoint_elements = explode('/', $handler->endpoint);
                $handler_endpoint_path = [];
                $handler_endpoint_properties = [];
                for ($i = 0; $i < count($handler_endpoint_elements); $i++) {
                    $field = $handler_endpoint_elements[$i];

                    if (
                        strpos($field, '{') === 0 &&
                        strpos($field, '}') === (strlen($field) - 1)
                    ) {
                        $field = str_replace('{', '', $field);
                        $field = str_replace('}', '', $field);

                        $handler_endpoint_properties[$i] = $field;
                    } else {
                        $handler_endpoint_path[$i] = $field;
                    }
                }

                $handler_endpoint_without_properties = implode(
                    $handler_endpoint_path,
                    '/'
                );

                unset($handler_endpoint_path);

                // Check if the requested endpoint matches the request handler.
                if (
                    strpos(
                        $endpoint,
                        $handler_endpoint_without_properties
                    ) !== false
                ) {
                    // Match the fields in the endpoint with the properties
                    // in the handler's endpoint definition.
                    $endpoint_elements = explode('/', $endpoint);
                    if (
                        count(
                            $endpoint_elements
                        ) != count(
                            $handler_endpoint_elements
                        )
                    ) {
                        $response = (new SynfulException(500, 1018))->response;
                        sf_respond($response->code, $response->serialize());
                        exit;
                    }

                    foreach (
                        $handler_endpoint_properties as $key => $property
                    ) {
                        $fields[$property] = $endpoint_elements[$key];
                    }

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

        $handler = Synful::$request_handlers[$selected_handler];

        $response = Synful::handleRequest(
            $handler,
            $input,
            $fields,
            Synful::getClientIP()
        );

        $all_middleware = sf_conf(
            'system.global_middleware'
        );

        if (property_exists($handler, 'middleware')) {
            if (! is_array($handler->middleware)) {
                throw new SynfulException(500, 1017);
            }

            $all_middleware = $all_middleware + $handler->middleware;
        }

        foreach ($all_middleware as $middleware) {
            $middleware = new $middleware;
            $middleware->after($response);
        }

        if ($response->serializer == null) {
            $serializer = sf_conf('system.serializer');
            $serializer = new $serializer;

            if (property_exists($handler, 'serializer')) {
                $serializer = new $handler->serializer;
            }

            $response->setSerializer($serializer);
        }

        header('Content-Type: '.$response->serializer->content_type);
        sf_respond(
            $response->code,
            $response->serialize(),
            false,
            $response->headers
        );
    }
}
