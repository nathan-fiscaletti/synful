<?php

namespace Synful\Util\Framework;

use stdClass;
use Synful\Synful;
use Synful\Util\DataManagement\Models\APIKey;

/**
 * Class used as middle man for key authentication and request validation.
 */
class Validator
{
    /**
     * Validates that the assigned handler is valid in the system.
     *
     * @param  array                           $data
     * @param  \Synful\Util\Framework\Response $response
     * @return bool
     */
    public function validateHandler(array &$data, Response &$response)
    {
        $response->request = $data['request'];
        $return = false;

        if (! empty($data['handler'])) {
            if (file_exists('./src/Synful/RequestHandlers/'.$data['handler'].'.php')) {
                $return = true;
            } else {
                throw new SynfulException($response, 500, 1001);
            }
        } else {
            throw new SynfulException($response, 500, 1002);
        }

        return $return;
    }

    /**
     * Validate a request with the system.
     *
     * @param  array                           $data
     * @param  \Synful\Util\Framework\Response $response
     * @return bool
     */
    public function validateRequest(array &$data, Response &$response)
    {
        $return = false;

        if (! empty($data['request'])) {
            if ($data['request'] instanceof stdClass) {
                try {
                    $data['request'] = (array) $data['request'];
                    if (is_array($data['request'])) {
                        $return = true;
                    } else {
                        throw new SynfulException($response, 400, 1003);
                    }
                } catch (\Exception $e) {
                    throw new SynfulException($response, 400, 1003);
                }
            } else {
                throw new SynfulException($response, 400, 1004);
            }
        } else {
            throw new SynfulException($response, 400, 1005);
        }

        return $return;
    }

    /**
     * Validates the authentication of the request.
     *
     * @param  array                                               $data
     * @param  \Synful\Util\Framework\Response                     $response
     * @param  object                                              $api_key
     * @param  \Synful\RequestHandlers\Interfaces\RequestHandler   $handler
     * @param  string                                              $ip
     * @return bool
     */
    public function validateAuthentication(&$data, &$response, &$api_key, &$handler, &$ip)
    {
        $return = true;
        if (! is_null($handler)) {
            if (! sf_conf('security.allow_public_requests') ||
                ! (property_exists($handler, 'is_public') && $handler->is_public)) {
                $return = false;
                if (! empty($data['user'])) {
                    if (! empty($data['key'])) {
                        if (APIKey::keyExists($data['user'])) {
                            $api_key = APIKey::getkey($data['user']);
                            $response->requesting_email = $api_key->email;
                            if (property_exists($handler, 'white_list_keys')) {
                                if (is_array($handler->white_list_keys)) {
                                    if (in_array($api_key->email, $handler->white_list_keys)) {
                                        if ($api_key->enabled) {
                                            if (
                                                $api_key->authenticate(
                                                    $data['key'],
                                                    (property_exists($handler, 'security_level'))
                                                        ? $handler->security_level
                                                        : 0
                                                )
                                            ) {
                                                return $this->validateFireWall($api_key, $response, $ip);
                                            } else {
                                                throw new SynfulException($response, 400, 1006);
                                            }
                                        } else {
                                            throw new SynfulException($response, 400, 1007);
                                        }
                                    } else {
                                        throw new SynfulException($response, 400, 1008);
                                    }
                                } else {
                                    if ($api_key->enabled) {
                                        if (
                                            $api_key->authenticate(
                                                $data['key'],
                                                (property_exists($handler, 'security_level'))
                                                    ? $handler->security_level
                                                    : 0
                                            )
                                        ) {
                                            return $this->validateFireWall($api_key, $response, $ip);
                                        } else {
                                            throw new SynfulException($response, 400, 1006);
                                        }
                                    } else {
                                        throw new SynfulException($response, 400, 1007);
                                    }
                                }
                            } else {
                                if ($api_key->enabled) {
                                    if (
                                        $api_key->authenticate(
                                            $data['key'],
                                            (property_exists($handler, 'security_level'))
                                                ? $handler->security_level
                                                : 0
                                        )
                                    ) {
                                        return $this->validateFireWall($api_key, $response, $ip);
                                    } else {
                                        throw new SynfulException($response, 400, 1006);
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
            throw new SynfulException($response, 500, 1001);
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
