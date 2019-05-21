<?php

namespace Synful\Middleware;

use Synful\Framework\Route;
use Synful\Framework\Request;
use Synful\Data\Models\APIKey;
use Synful\Framework\Response;
use Synful\Framework\Middleware;
use Synful\Framework\SynfulException;

/**
 * Custom Middleware implementation.
 */
class APIKeyValidation implements Middleware
{
    /**
     * The property key used to associate the
     * Middleware Route properties.
     * 
     * @var string
     */
    public $key = 'api_key';

    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Framework\Request $request
     */
    public function before(Request $request)
    {
        $this->validateRequest($request);
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Framwork\Response $response
     */
    public function after(Response $response)
    {
        // Don't implement
    }

    /**
     * Validate the request.
     *
     * @param  \Synful\Framework\Request $request
     */
    private function validateRequest(Request $request)
    {
        // Validate the Request Headers.
        if (empty($request->header('Synful-Auth'))) {
            throw new SynfulException(400, 1010);
        }

        if (empty($request->header('Synful-Key'))) {
            throw new SynfulException(400, 1009);
        }

        // Assign the user and key to the values of the request headers.
        $user = $request->header('Synful-Auth');
        $key = $request->header('Synful-Key');

        // Load the API Key
        $api_key = APIKey::getApikey($user);

        // Validate the the API key exists.
        if ($api_key === null) {
            throw new SynfulException(400, 1006);
        }

        // Check rate limit for API key
        /*
        if (sf_conf('rate.per_key')) {
            $api_key_rl = $api_key->getRateLimit();
            if (! $api_key_rl->isUnlimited()) {
                if ($api_key_rl->isLimited($request->ip)) {
                    $response = (new SynfulException(500, 1030))->response;
                    sf_respond($response->code, $response->serialize());
                    exit;
                }
            }
        }
        */

        // Assign the API key to a variable and
        // update the request.
        $request->auth = $api_key->auth;

        // Validate that the API key is enabled.
        if (! $api_key->enabled) {
            throw new SynfulException(400, 1007);
        }

        // Validate API Key endpoint access array.
        /*
        if (
            ! in_array(
                $handler->endpoint,
                $api_key->getRequestHandlersParsed()
            ) &&

            // Check for wildcard access
            ! in_array(
                '*',
                $api_key->getRequestHandlersParsed()
            )
        ) {
            throw new SynfulException(400, 1032);
        }
        */

        // Validate the API Key Security.
        $this->validateApiKeySecurity(
            $request->route,
            $api_key,
            $key,
            $request->ip
        );
    }

    /**
     * Validate an API key.
     *
     * @param  \Synful\Framework\Route           $route
     * @param  APIKey                            $api_key
     * @param  string                            $key
     * @param  string                            $ip
     *
     * @throws \Synful\Framework\SynfulException
     */
    private function validateApiKeySecurity(
        $route,
        APIKey $api_key,
        string $key,
        string $ip
    ) {
        $security_level = $route->middlewareProperty(
            $this,
            'security_level'
        );

        $security_level = is_null($security_level) ? 0 : $security_level;

        $security_result = $api_key->authenticate(
            $key,
            $security_level
        );

        switch ($security_result) {
            case -1: {
                throw new SynfulException(400, 1006);
            }

            case 0: {
                throw new SynfulException(400, 1003);
            }

            case 1: {
                $this->validateFireWall($api_key, $ip);
            }
        }
    }

    /**
     * Validate the firewall of an APIKey.
     *
     * @param  \Synful\Data\Models\APIKey $api_key
     * @param  string                               $ip
     * @throws \Synful\Framework\SynfulException
     */
    private function validateFireWall(APIKey &$api_key, string $ip)
    {
        if ($api_key->whitelist_only) {
            if (! $api_key->isFirewallWhiteListed($ip)) {
                throw new SynfulException(500, 1011);
            }
        }

        if ($api_key->isFirewallBlackListed($ip)) {
            throw new SynfulException(500, 1012);
        }
    }
}
