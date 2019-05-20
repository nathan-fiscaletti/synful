<?php

namespace App\Controllers;

use Synful\Framework\Controller;
use Synful\Serializers\JSONSerializer;

/**
 * This Class houses a handful of examples on how
 * one might implement a Controller as well as
 * some useful information about methods
 * available to you through the Request class.
 * 
 * @author Nathan Fiscaletti <nathan.fiscaletti@gmail.com>
 * @link https://github.com/nathan-fiscaletti/synful/
 */
final class Example implements Controller
{
    /**
     * Example: Retrieve a clients IP address.
     *
     * @see routes.yaml - /example/ip
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function getIp(\Synful\Framework\Request $request)
    {
        return [
            'ip' => $request->ip,
        ];
    }

    /**
     * Example: Set a custom header.
     *
     * @see routes.yaml - /example/header
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function header(\Synful\Framework\Request $request)
    {
        return sf_response(
            200,
            [
                'success' => true,
            ]
        )->setHeader('Test', 'It Worked!');
    }

    /**
     * Example: Return a custom HTTP code.
     *
     * @see routes.yaml - /example/httpcode
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function httpCode(\Synful\Framework\Request $request)
    {
        return sf_response(
            401,
            [
                'error' => 'You are not authorized to be here.',
            ]
        );
    }

    /**
     * Example: Retrieve all input.
     *
     * @see routes.yaml - /example/inputs
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function inputs(\Synful\Framework\Request $request)
    {
        return [
            'inputs' => $request->inputs(),
        ];
    }

    /**
     * Example: Retrieve specific input.
     *
     * @see routes.yaml - /example/input
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function input(\Synful\Framework\Request $request)
    {
        $name = $request->input('name');

        if ($name == null) {
            return sf_response(
                500,
                [
                    'error' => 'Missing name parameter.',
                ]
            );
        } else {
            return [
                'your_name_is' => $name,
            ];
        }
    }

    /**
     * Example: Set a custom response serializer.
     * 
     * @see routes.yaml - /example/serializer
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function serializer(\Synful\Framework\Request $request)
    {
        // Input sent to the Route pointing to this
        // Controller should be in CSV format.
        //
        // i.e. curl -d $'name,age\n"Nathan Fisc",18\nJim,23' 127.0.0.1/example/serializer
        //
        // Output will be returned as JSON instead of CSV.
        return sf_response(200, [
            'received' => $request->inputs(),
        ])->setSerializer(new JSONSerializer);
    }

    /**
     * Example: URL parameters.
     * 
     * @see routes.yaml - /example/parameters/{name}
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function parameters(\Synful\Framework\Request $request)
    {
        return [
            'message' => 'Your name is: '.$request->field('name'),
        ];
    }

    /**
     * Example: Generate a download.
     * 
     * @see routes.yaml - /example/download
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function download(\Synful\Framework\Request $request)
    {
        return sf_response(
            200,
            [
                'data' => 'This is the content of the downloaded file.',
            ]
        )->downloadableAs('text.txt');
    }

    /**
     * Example: Display an HTML template.
     * 
     * @see routes.yaml - /example/template
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function template(\Synful\Framework\Request $request)
    {
        $name = $request->input('name') == null
                    ? 'John Doe'
                    : $request->input('name');

        return new \Synful\Templating\Template(
            'Example.html',
            [
                'name' => $name // cannot be null
            ]
        );
    }

    /**
     * Example: Return a response after the RateLimit
     *          middleware has been applied.
     * 
     * @see routes.yaml - /example/middleware/ratelimit
     *
     * @param \Synful\Framework\Request $request
     * @return \Synful\Framework\Response|array
     */
    public function ratelimit(\Synful\Framework\Request $request)
    {
        return ['message' => 'Success'];
    }
}