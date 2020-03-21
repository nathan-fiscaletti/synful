<?php /** @noinspection PhpUnusedParameterInspection */

namespace App\Controllers;

use Exception;
use Synful\Framework\Controller;
use Synful\Framework\Request;
use Synful\Framework\Response;
use Synful\Framework\SynfulException;
use Synful\Serializers\JSONSerializer;
use Synful\Templating\Template;

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
     * @see routes.yaml - /example/getip
     *
     * @param Request $request
     * @return Response|array
     */
    public function getIp(Request $request)
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
     * @param Request $request
     * @return Response|array
     * @throws SynfulException
     */
    public function header(Request $request)
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
     * @param Request $request
     * @return Response|array
     * @throws SynfulException
     */
    public function httpCode(Request $request)
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
     * @param Request $request
     * @return Response|array
     */
    public function inputs(Request $request)
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
     * @param Request $request
     * @return Response|array
     * @throws SynfulException
     */
    public function input(Request $request)
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
     * @param Request $request
     * @return Response|array
     * @throws SynfulException
     */
    public function serializer(Request $request)
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
     * @param Request $request
     * @return Response|array
     */
    public function parameters(Request $request)
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
     * @param Request $request
     * @return Response|array
     * @throws SynfulException
     */
    public function download(Request $request)
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
     * @param Request $request
     *
     * @return Template
     *
     * @throws SynfulException
     * @throws Exception
     * @see routes.yaml - /example/template
     */
    public function template(Request $request)
    {
        $name = $request->input('name') == null
                    ? 'John Doe'
                    : $request->input('name');

        return new Template(
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
     * @param Request $request
     * @return Response|array
     */
    public function rateLimit(Request $request)
    {
        return ['message' => 'Success'];
    }
}