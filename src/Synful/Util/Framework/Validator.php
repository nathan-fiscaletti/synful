<?php

namespace Synful\Util\Framework;

use Synful\Util\DataManagement\Models\APIKey;

/**
 * Class used as middle man for key authentication and request validation.
 */
class Validator
{
    /**
     * Validates the authentication of the request.
     *
     * @param  \Synful\Util\Framwork\Response                      $response
     * @param  object                                              $api_key
     * @param  \Synful\RequestHandlers\Interfaces\RequestHandler   $handler
     * @param  string                                              $ip
     * @return bool
     */
    public function validateAuthentication(&$response, &$api_key, &$handler, &$ip)
    {
        $return = true;
        if (! is_null($handler)) {
            if (! sf_conf('security.allow_public_requests') ||
                ! (property_exists($handler, 'is_public') && $handler->is_public)) {
                $return = false;
                if (! empty($response->request_headers['Synful-User'])) {
                    if (! empty($response->request_headers['Synful-Key'])) {
                        $user = $response->request_headers['Synful-User'];
                        $key = $response->request_headers['Synful-Key'];
                        if (APIKey::keyExists($user)) {
                            $api_key = APIKey::getkey($user);
                            $response->requesting_email = $api_key->email;
                            if (property_exists($handler, 'white_list_keys')) {
                                if (is_array($handler->white_list_keys)) {
                                    if (in_array($api_key->email, $handler->white_list_keys)) {
                                        if ($api_key->enabled) {
                                            $security_result = $api_key->authenticate(
                                                $key,
                                                (property_exists($handler, 'security_level'))
                                                    ? $handler->security_level
                                                    : 0
                                            );

                                            switch ($security_result) {
                                                case -1: {
                                                    throw new SynfulException($response, 400, 1006);
                                                }

                                                case 0: {
                                                    throw new SynfulException($response, 400, 1003);
                                                }

                                                case 1: {
                                                    return $this->validateFireWall($api_key, $response, $ip);
                                                }
                                            }
                                        } else {
                                            throw new SynfulException($response, 400, 1007);
                                        }
                                    } else {
                                        throw new SynfulException($response, 400, 1008);
                                    }
                                } else {
                                    if ($api_key->enabled) {
                                        $security_result = $api_key->authenticate(
                                            $key,
                                            (property_exists($handler, 'security_level'))
                                                ? $handler->security_level
                                                : 0
                                        );

                                        switch ($security_result) {
                                            case -1: {
                                                throw new SynfulException($response, 400, 1006);
                                            }

                                            case 0: {
                                                throw new SynfulException($response, 400, 1003);
                                            }

                                            case 1: {
                                                return $this->validateFireWall($api_key, $response, $ip);
                                            }
                                        }
                                    } else {
                                        throw new SynfulException($response, 400, 1007);
                                    }
                                }
                            } else {
                                if ($api_key->enabled) {
                                    $security_result = $api_key->authenticate(
                                        $key,
                                        (property_exists($handler, 'security_level'))
                                            ? $handler->security_level
                                            : 0
                                    );

                                    switch ($security_result) {
                                        case -1: {
                                            throw new SynfulException($response, 400, 1006);
                                        }

                                        case 0: {
                                            throw new SynfulException($response, 400, 1003);
                                        }

                                        case 1: {
                                            return $this->validateFireWall($api_key, $response, $ip);
                                        }
                                    }
                                } else {
                                    throw new SynfulException($response, 400, 1007);
                                }
                            }
                        } else {
                            throw new SynfulException($response, 400, 1006);
                        }
                    } else {
                        throw new SynfulException($response, 400, 1009);
                    }
                } else {
                    throw new SynfulException($response, 400, 1010);
                }
            }
        } else {
            throw new SynfulException($response, 404, 1001);
        }

        return $return;
    }

    /**
     * Validate the firewall of an APIKey.
     *
     * @param  \Synful\DataManagement\Models\APIKey $api_key
     * @param  \Synful\Util\Framework\Response      $response
     * @param  string                               $ip
     * @return bool
     */
    public function validateFireWall(APIKey &$api_key, Response &$response, string $ip)
    {
        $return = true;
        if ($api_key->whitelist_only) {
            if (! $api_key->isFirewallWhiteListed($ip)) {
                throw new SynfulException($response, 500, 1011);
            }
        }

        if ($return && $api_key->isFirewallBlackListed($ip)) {
            throw new SynfulException($response, 500, 1012);
        }

        return $return;
    }
}
