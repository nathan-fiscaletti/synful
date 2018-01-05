<?php

namespace Synful\Util\Framework;

use Synful\Util\DataManagement\Models\APIKey;

/**
 * Class used as middle man for key authentication and request validation.
 */
class Validator
{
    /**
     * Validates a Request.
     *
     * @param  \Synful\Util\Framwork\Request                       $request
     * @param  \Synful\RequestHandlers\Interfaces\RequestHandler   $handler
     * @return bool
     * @throws \Synful\Util\Framework\SynfulException
     */
    public function validateRequest(
        &$request,
        &$handler
    ) {
        $return = true;

        // Verify we are working with a real RequestHandler.
        if (is_null($handler)) {
            throw new SynfulException(404, 1001);
        }

        // Check if we should allow public requests,
        // and if so, check if the request handler is public.
        //
        // For pulic request handlers, we will simply return validated.
        if (
            ! sf_conf('security.allow_public_requests') ||
            ! (property_exists($handler, 'is_public') && $handler->is_public)
        ) {
            // Validate the Request Headers.
            if (empty($request->headers['Synful-User'])) {
                throw new SynfulException(400, 1010);
            }

            if (empty($request->headers['Synful-Key'])) {
                throw new SynfulException(400, 1009);
            }

            // Assign the user and key to the values of the request headers.
            $user = $request->headers['Synful-User'];
            $key = $request->headers['Synful-Key'];

            // Validate the the API key exists.
            if (! APIKey::keyExists($user)) {
                throw new SynfulException(400, 1006);
            }

            // Assign the API key to a variable and
            // update the request.
            $api_key = APIKey::getkey($user);
            $request->email = $api_key->email;

            // Validate that the API key is enabled.
            if (! $api_key->enabled) {
                throw new SynfulException(400, 1007);
            }

            // Validate the whitelist.
            if (
                property_exists($handler, 'white_list_keys') &&
                is_array($handler->white_list_keys)
            ) {
                if (! in_array($api_key->email, $handler->white_list_keys)) {
                    throw new SynfulException(400, 1008);
                }
            }

            // Validate the API Key Security.
            $return = $this->validateApikeySecurity(
                $handler,
                $api_key,
                $key,
                $request->ip
            );
        }

        return $return;
    }

    /**
     * Validate an API key.
     *
     * @param  \Synful\RequestHandlers\Interfaces\RequestHandler   $handler
     * @param  APIKey                                              $api_key
     * @param  string                                              $key
     * @param  string                                              $ip
     * @return bool
     * @throws \Synful\Util\Framework\SynfulException
     */
    private function validateApikeySecurity(
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
                return $this->validateFireWall($api_key, $ip);
            }
        }
    }

    /**
     * Validate the firewall of an APIKey.
     *
     * @param  \Synful\DataManagement\Models\APIKey $api_key
     * @param  string                               $ip
     * @return bool
     * @throws \Synful\Util\Framework\SynfulException
     */
    private function validateFireWall(APIKey &$api_key, string $ip)
    {
        $return = true;
        if ($api_key->whitelist_only) {
            if (! $api_key->isFirewallWhiteListed($ip)) {
                throw new SynfulException(500, 1011);
            }
        }

        if ($return && $api_key->isFirewallBlackListed($ip)) {
            throw new SynfulException(500, 1012);
        }

        return $return;
    }
}
