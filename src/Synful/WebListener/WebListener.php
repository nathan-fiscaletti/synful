<?php

namespace Synful\WebListener;

use Synful\Synful;
use Synful\Framework\SynfulException;

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

        $endpoint = $_GET['_synful_ep_'];
        $found_endpoint = false;
        $selected_route = null;
        $fields = [];

        foreach (Synful::$routes as $route) {
            if (strtolower($route->method) != strtolower($_SERVER['REQUEST_METHOD'])) {
                continue;
            }

            // Separate the parameters from the endpoint.
            $route_endpoint_elements = explode('/', $route->path);
            $route_endpoint_path = [];
            $route_endpoint_properties = [];
            for ($i = 0; $i < count($route_endpoint_elements); $i++) {
                $field = $route_endpoint_elements[$i];

                if (
                    strpos($field, '{') === 0 &&
                    strpos($field, '}') === (strlen($field) - 1)
                ) {
                    $field = str_replace('{', '', $field);
                    $field = str_replace('}', '', $field);

                    $route_endpoint_properties[$i] = $field;
                } else {
                    $route_endpoint_path[$i] = $field;
                }
            }

            $route_endpoint_without_properties = implode(
                $route_endpoint_path,
                '/'
            );

            unset($route_endpoint_path);

            // Check if the requested endpoint matches the route.
            // If the route endpoint without properties is empty,
            // we will check the property count.
            //
            // A route with an empty prefix will override all other
            // routes in the system.
            if (
                $route_endpoint_without_properties == '' ||
                strpos(
                    $endpoint,
                    $route_endpoint_without_properties
                ) !== false
            ) {
                // Match the fields in the endpoint with the properties
                // in the route's endpoint definition.
                $endpoint_elements = explode('/', $endpoint);
                if (
                    count(
                        $endpoint_elements
                    ) != count(
                        $route_endpoint_elements
                    )
                ) {
                    $response = (new SynfulException(500, 1018))->response;
                    sf_respond($response->code, $response->serialize());
                    exit;
                }

                foreach (
                    $route_endpoint_properties as $key => $property
                ) {
                    $fields[$property] = $endpoint_elements[$key];
                }

                $found_endpoint = true;
                $selected_route = $route->path;
                break;
            }
        }

        if (! $found_endpoint) {
            $response = (new SynfulException(404, 1001))->response;
            sf_respond($response->code, $response->serialize());
            exit;
        }

        $input = file_get_contents('php://input');

        // Ger parameters are stored in a different location.
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $params = $_GET;

            // Remove System variables
            unset($params['_synful_ep_']);

            $input = (new \Synful\Serializers\URLSerializer)->serialize($params);
        }

        $route = Synful::$routes[$selected_route];

        $response = Synful::handleRequest(
            $route,
            $input,
            $fields,
            Synful::getClientIP()
        );

        $all_middleware = sf_conf(
            'system.global_middleware'
        );

        if (property_exists($route, 'middleware')) {
            if (! is_array($route->middleware)) {
                throw new SynfulException(500, 1017);
            }

            $all_middleware = $all_middleware + $route->middleware;
        }

        foreach ($all_middleware as $middleware) {
            $middleware = new $middleware;
            $middleware->after($response);
        }

        if ($response->serializer == null) {
            $serializer = sf_conf('system.serializer');
            $serializer = new $serializer;

            if (
                property_exists($route, 'serializer') &&
                class_exists($route->serializer)
            ) {
                $serializer = new $route->serializer;
            }

            $response->setSerializer($serializer);
        }

        header('Content-Type: '.$response->serializer->content_type);
        sf_respond(
            $response->code,
            $response->serialize(),
            $response->headers()
        );
    }
}
