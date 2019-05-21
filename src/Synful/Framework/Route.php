<?php

namespace Synful\Framework;

/**
 * Class for managing a Route.
 */
class Route
{
    use ParamObject;

    /**
     * The path for this Route.
     * 
     * @var string
     */
    public $path;

    /**
     * The method for this Route.
     * 
     * @var string
     */
    public $method;

    /**
     * The Controller to which this route will point.
     * 
     * @var string
     */
    public $controller;

    /**
     * The function we will call on the Controller that
     * this Route points to.
     * 
     * @var string
     */
    public $call;

    /**
     * The Middleware for this Route.
     * 
     * @var array
     */
    public $middleware = [];

    /**
     * The Serializer for this Route.
     * 
     * @var string
     */
    public $serializer;

    /**
     * The Rate Limit for this Route.
     * 
     * @var array
     */
    public $rate_limit = [
        'requests' => 0,
        'per_seconds' => 0
    ];

    /**
     * The properties that Middleware can supply.
     * 
     * @var array
     */
    public $middleware_props = [];

    /**
     * Retrieve the property for the specified middleware.
     * 
     * @param \Synful\Framework\Middleware $middleware
     * @param string                       $property
     * 
     * @return mixed
     */
    public function middlewareProperty($middleware, $property)
    {
        if (property_exists($middleware, 'key')) {
            if (array_key_exists($middleware->key, $this->middleware_props)) {
                if (array_key_exists($property, $this->middleware_props[$middleware->key])) {
                    return $this->middleware_props[$middleware->key][$property];
                }
            }
        }

        return null;
    }

    /**
     * Check if this Route has a specific Middelware.
     * 
     * @param string $middleware
     * 
     * @return bool
     */
    public function hasMiddleware($middleware)
    {
        return in_array($middleware, $this->middleware);
    }

    /**
     * Build a new Route from a data array.
     * 
     * @param string $path
     * @param array  $route_data
     * 
     * @return \Synful\Framework\Route
     */
    public static function buildRoute($path, $route_data)
    {
        $route = new Route([
            'path' => $path,  
            'method' => array_key_exists('method', $route_data)
                ? $route_data['method']
                : 'POST',
            'controller' => array_key_exists('controller', $route_data) 
                ? explode('@', $route_data['controller'])[0]
                : null,
            'call' => array_key_exists('controller', $route_data)
                ? explode('@', $route_data['controller'])[1] 
                : null,
            'middleware' => array_key_exists('middleware', $route_data) 
                ? $route_data['middleware'] 
                : [],
            'serializer' => array_key_exists('serializer', $route_data)
                ? $route_data['serializer']
                : null,
            'rate_limit' => array_key_exists('rate_limit', $route_data)
                ? $route_data['rate_limit']
                : ['requests' => 0, 'per_seconds' => 0]
        ]);

        if (
            array_key_exists('middleware', $route_data) &&
            is_array($route_data['middleware'])
        ) {
            foreach ($route_data['middleware'] as $middlewareClass) {
                if (class_exists($middlewareClass)) {
                    if (property_exists($middlewareClass, 'key')) {
                        $property_key = (new $middlewareClass)->key;
                        if (array_key_exists($property_key, $route_data)) {
                            $route->middleware_props[$property_key] 
                                = $route_data[$property_key];
                        }
                    }
                }
            }
        }

        return $route;
    }
}