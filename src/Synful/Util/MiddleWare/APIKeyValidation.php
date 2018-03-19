<?php

namespace Synful\Util\MiddleWare;

use Synful\Util\Framework\Request;
use Synful\Util\Data\Models\APIKey;
use Synful\Util\Framework\Response;
use Synful\Util\Framework\MiddleWare;
use Synful\Util\Framework\RequestHandler;
use Synful\Util\Framework\SynfulException;

/**
 * Custom MiddleWare implementation.
 */
class APIKeyValidation implements MiddleWare
{
    /**
     * Perform the specified action on the request before
     * passing it to the RequestHandler.
     *
     * @param  \Synful\Util\Framework\Request        $request
     * @param  \Synful\Util\Framework\RequestHandler $handler
     * @return bool
     */
    public function before(Request $request, RequestHandler $handler)
    {
        $this->validateRequest($request, $handler);
    }

    /**
     * Perform the specified action on a Response before
     * passing it back to the client.
     *
     * @param \Synful\Util\Framwork\Response $response
     */
    public function after(Response $response)
    {
        // Don't implement
    }

    /**
     * Validate the request.
     *
     * @param  Request        $request
     * @param  RequestHandler $handler
     */
    private function validateRequest(Request $request, RequestHandler $handler)
    {
        // Validate the Request Headers.
        if (empty($request->headers['Synful-Auth'])) {
            throw new SynfulException(400, 1010);
        }

        if (empty($request->headers['Synful-Key'])) {
            throw new SynfulException(400, 1009);
        }

        // Assign the user and key to the values of the request headers.
        $user = $request->headers['Synful-Auth'];
        $key = $request->headers['Synful-Key'];

        // Load the API Key
        $api_key = APIKey::getApikey($user);

        // Validate the the API key exists.
        if ($api_key === null) {
            throw new SynfulException(400, 1006);
        }

        // Assign the API key to a variable and
        // update the request.
        $request->auth = $api_key->auth;

        // Validate that the API key is enabled.
        if (! $api_key->enabled) {
            throw new SynfulException(400, 1007);
        }

        // Validate the whitelist.
        if (
            property_exists($handler, 'white_list_keys') &&
            is_array($handler->white_list_keys)
        ) {
            if (! in_array($api_key->auth, $handler->white_list_keys)) {
                throw new SynfulException(400, 1008);
            }
        }

        // Validate the API Key Security.
        $this->validateApiKeySecurity(
            $handler,
            $api_key,
            $key,
            $request->ip
        );
    }

    /**
     * Validate an API key.
     *
     * @param  \Synful\RequestHandlers\Interfaces\RequestHandler   $handler
     * @param  APIKey                                              $api_key
     * @param  string                                              $key
     * @param  string                                              $ip
     * @throws \Synful\Util\Framework\SynfulException
     */
    private function validateApiKeySecurity(
        $handler,
        APIKey $api_key,
        string $key,
        string $ip
    ) {
        $security_result = $api_key->authenticate(
            $key,
            (property_exists($handler, 'security_level'))
                ? $handler->security_level
                : 0
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
     * @throws \Synful\Util\Framework\SynfulException
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
