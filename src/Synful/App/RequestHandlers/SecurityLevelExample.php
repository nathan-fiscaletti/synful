<?php

namespace Synful\App\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\MiddleWare\APIKeyValidation;

/**
 * Class used to demonsrate security level.
 */
class SecurityLevelExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/secure';

    /**
     * Implement the APIKeyValidation middleware
     * in order to require an API key to access
     * this RequestHandler. This is also used to
     * parse the security_level property.
     *
     * @var array
     */
    public $middleware = [
        APIKeyValidation::class,
    ];

    /**
     * Set the security level for the RequestHandler.
     * Only API keys with this security level or
     * higher can access this RequestHandler.
     *
     * Note: Must implement the APIKeyValidation middleware.
     *
     * @var int
     */
    public $security_level = 4;

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        return [
            'message' => 'This API key has a security level equal to or greater than 4.',
        ];
    }
}
