<?php

namespace Synful\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\Serializers\CSVSerializer;

/**
 * New Request Handler Class.
 */
class SerializerExample implements RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * You can also add fields to the endpoint
     * by encapsulating them with `{field_name}`.
     *
     * You can access these fields using
     * `$request->field('field_name')`.
     *
     * @var string
     */
    public $endpoint = 'example/serializer';

    /**
     * Override the serializer used for
     * this request handler. This will 
     * override whatever setting is in 
     * the System.php configuration.
     * 
     * @var string
     */
    public $serializer = CSVSerializer::class;

    /**
     * Function for handling request and returning a response.
     *
     * @param Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function handleRequest(Request $request)
    {
        // This array will be passed through the CSVSerializer
        // before it is returned.
        // 
        // Input sent to this RequestHandler should be CSV
        // formatted. 
        // 
        // i.e. curl -d 'value1,value2' 127.0.0.1/example/serialize
        // 
        return [
            'a first value',
            $request->input('1'),
        ];
    }
}
