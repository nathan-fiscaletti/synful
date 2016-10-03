<?php

namespace Synful;

use Synful\DataManagement\Models\APIKey;
use Synful\IO\IOFUnctions;
use Synful\IO\LogLevel;
use Synful\RequestHandlers\Interfaces\RequestHandler;
use Synful\Util\SynfulException;
use stdClass;

/**
 * Class used as middle man for key authentication and request validation.
 */
class Controller
{
    /**
     * Passes a JSON Request through the desired request handlers, validates authentication
     * and request integrity and returns a response.
     *
     * @param  string   $request
     * @param  string   $ip
     * @return \Synful\Response
     */
    public function handleRequest($request, $ip)
    {
        $data = (array) json_decode($request);
        $response = new Response(['requesting_ip' => $ip]);

        try {
            if ($this->validateRequest($data, $response) && $this->validateHandler($data, $response)) {
                $handler = &Synful::$request_handlers[$data['handler']];
                $api_key = null;
                if ($this->validateAuthentication($data, $response, $api_key, $handler, $ip)) {
                    $handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);
                }
            }
        } catch (SynfulException $synfulException) {
            $response = $synfulException->response;
        }

        return $response;
    }

    private function respond(&$response, $code, $responseData, $validated = true)
    {
        $response->code = $code;
        $response->setArr($responseData);
        return $validated;
    }

    /**
     * Generates a master API Key if one does not already exist.
     *
     * @return \Synful\DataManagement\Models\APIKey
     */
    public function generateMasterKey()
    {
        $ret = null;
        if (! APIKey::isMasterSet()) {
            IOFunctions::out(LogLevel::INFO, 'No master key found. Generating new master key.');
            $apik = APIKey::addNew(
                Synful::$config->get('security.name'),
                Synful::$config->get('security.email'),
                0,
                1,
                true
            );
            if ($apik == null) {
                IOFunctions::out(LogLevel::WARN, 'Failed to get master key.');
            }
            $ret = $apik;
        } else {
            $ret = APIKey::getMasterKey();
        }

        return $ret;
    }

    /**
     * Validates that the assigned handler is valid in the system.
     *
     * @param  array    $data
     * @param  \Synful\Response $response
     * @return bool
     */
    private function validateHandler(array &$data, Response &$response)
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
     * @param  array    $data
     * @param  \Synful\Response $response
     * @return bool
     */
    private function validateRequest(array &$data, Response &$response)
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
     * @param  Response                                            $response
     * @param  object                                              $api_key
     * @param  \Synful\RequestHandlers\Interfaces\RequestHandler   $handler
     * @param  string                                              $ip
     * @return bool
     */
    private function validateAuthentication(&$data, &$response, &$api_key, &$handler, &$ip)
    {
        $return = true;
        if (!is_null($handler)) {
            if (! Synful::$config->get('security.allow_public_requests') ||
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
                                            if ($api_key->authenticate($data['key'])) {
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
                                        if ($api_key->authenticate($data['key'])) {
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
                                    if ($api_key->authenticate($data['key'])) {
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
     * @param  \Synful\Response                     $response
     * @param  string                               $ip
     * @return bool
     */
    private function validateFireWall(APIKey &$api_key, Response &$response, string $ip)
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
