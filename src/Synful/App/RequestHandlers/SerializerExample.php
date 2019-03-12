<?php

namespace Synful\App\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\Serializers\CSVSerializer;
use Synful\Util\Serializers\JSONSerializer;

/**
 * Class used to demonstrate custom serializers.
 */
class SerializerExample extends RequestHandler
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
     * Handles a POST request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function post(Request $request)
    {
        // Input sent to this RequestHandler should be CSV
        // formatted.
        //
        // i.e. curl -d $'name,age\n"Nathan Fisc",18\nJim,23' 127.0.0.1/example/serializer
        //
        // Output will be returned as JSON instead of CSV.
        return sf_response(200, [
            'received' => $request->inputs(),
        ])->setSerializer(new JSONSerializer);;
    }
}
