<?php

namespace Synful;

use Synful\DataManagement\Models\APIKey;
use Synful\Response;
use Synful\IO\IOFUnctions;
use Synful\IO\LogLevel;
use Synful\RequestHandlers\Interfaces\RequestHandler;

use stdClass;
use Exception;

/**
 * Class used as middle man for key authentication and request validation
 */
class Controller
{

    /**
     * Passes a JSON Request through the desired request handlers and returns a response
     * Validates authentication and request integrity
     *
     * @param  String   $request
     * @param  Stripg   $ip
     * @return Response
     */
    public function handleRequest($request, $ip)
    {

        $data = (array)json_decode($request);
        $response = new Response(['requesting_ip' => $ip]);

        if ($this->validateRequest($data, $response) && $this->validateHandler($data, $response)) {
            $handler =& Synful::$request_handlers[$data['handler']];
            $api_key = null;
            if ($this->validateAuthentication($data, $response, $api_key, $handler, $ip)) {
                $handler->handleRequest($response, ($api_key == null) ? false : $api_key->is_master);
            }
        }

        return $response;
    }

    /**
     * Generates a master API Key if one does not already exist
     *
     * @return APIKey The generated APIKey
     */
    public function generateMasterKey()
    {
        $ret = null;
        if (!APIKey::isMasterSet()) {
            IOFunctions::out(LogLevel::INFO, 'No master key found. Generating new master key.');
            $apik = APIKey::addNew(
                Synful::$config['security']['name'],
                Synful::$config['security']['email'],
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
     * Validates that the assigned handler is valid in the system
     *
     * @param  Array    $data
     * @param  Response $response
     * @return Boolean
     */
    private function validateHandler(array &$data, Response &$response)
    {

        $response->request = $data['request'];

        $return = false;

        if (!empty($data['handler'])) {
            if (file_exists('./src/Synful/RequestHandlers/'.$data['handler'].'.php')) {
                $return = true;
            } else {
                $response->code = 500;
                $response->setResponse(
                    'error',
                    'Unknown Handler: '.$data['handler'].'.Handlers are case sensitive.'
                );
                $return = false;
            }
        } else {
            $response->code = 500;
            $response->setResponse(
                'error',
                'No handler defined.'
            );
            $return = false;
        }

        return $return;
    }

    /**
     * Validate a request with the system
     *
     * @param  Array    $data
     * @param  Response $response
     * @return Boolean
     */
    private function validateRequest(array &$data, Response &$response)
    {
        $return = false;

        if (!empty($data['request'])) {
            if ($data['request'] instanceof stdClass) {
                try {
                    $data['request'] = (Array)$data['request'];
                    if (is_array($data['request'])) {
                        $return = true;
                    } else {
                        $response->code = 400;
                        $response->setResponse('error', 'Bad request: Invalid request field supplied. Not array.');
                        $return = false;
                    }
                } catch (Exception $e) {
                    $response->code = 400;
                    $response->setResponse('error', 'Bad request: Invalid request field supplied. Not array.');
                    $return = false;
                }
            } else {
                $response->code = 400;
                $response->setResponse('error', 'Bad request: Invalid request field supplied. Not Object.');
                $return = false;
            }
        } else {
            $response->code = 400;
            $response->setResponse('error', 'Bad request: Missing request field');
            $return = false;
        }

        return $return;
    }

    /**
     * Validates the authentication of the request
     *
     * @param  Array            $data
     * @param  Response         $response
     * @param  Object           $api_key
     * @param  RequestHandler   $handler
     * @param  String           $ip
     * @return Boolean
     */
    private function validateAuthentication(&$data, &$response, &$api_key, &$handler, &$ip)
    {
        $return = true;

        if (!Synful::$config['security']['allow_public_requests'] ||
            !(property_exists($handler, 'is_public') && $handler->is_public)) {
            $return = false;
            if (!empty($data['user'])) {
                if (!empty($data['key'])) {
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
                                            $response->code = 400;
                                            $response->setResponse('error', 'Bad Request: Invalid user or key');
                                            $return = false;
                                        }
                                    } else {
                                        $response->code = 400;
                                        $response->setResponse('error', 'Bad Request: Key has been disabled');
                                        $return = false;
                                    }
                                } else {
                                    $response->code = 400;
                                    $response->setResponse(
                                        'error',
                                        'Bad Request: Key not whitelisted for specified request handler.'
                                    );
                                    $return = false;
                                }
                            } else {
                                if ($api_key->enabled) {
                                    if ($api_key->authenticate($data['key'])) {
                                        return $this->validateFireWall($api_key, $response, $ip);
                                    } else {
                                        $response->code = 400;
                                        $response->setResponse('error', 'Bad Request: Invalid user or key');
                                        $return = false;
                                    }
                                } else {
                                    $response->code = 400;
                                    $response->setResponse('error', 'Bad Request: Key has been disabled');
                                    $return = false;
                                }
                            }
                        } else {
                            if ($api_key->enabled) {
                                if ($api_key->authenticate($data['key'])) {
                                    return $this->validateFireWall($api_key, $response, $ip);
                                } else {
                                    $response->code = 400;
                                    $response->setResponse('error', 'Bad Request: Invalid user or key');
                                    $return = false;
                                }
                            } else {
                                $response->code = 400;
                                $response->setResponse('error', 'Bad Request: Key has been disabled');
                                $return = false;
                            }
                        }
                    } else {
                        $response->code = 400;
                        $response->setResponse('error', 'Bad Request: Invalid user or key');
                        $return = false;
                    }
                } else {
                    $response->code = 400;
                    $response->setResponse('error', 'Bad Request: No key defined');
                    $return = false;
                }
            } else {
                $response->code = 400;
                $response->setResponse('error', 'Bad Request: No user defined');
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Validate the firewall of an APIKey
     *
     * @param  APIKey   $api_key
     * @param  Response $response
     * @param  String   ip
     * @return Boolean
     */
    private function validateFireWall(APIKey &$api_key, Response &$response, String $ip)
    {
        if ($api_key->whitelist_only) {
            if (!$api_key->isFirewallWhiteListed($ip)) {
                $response->code = 500;
                $response->setResponse(
                    'error',
                    'Access Denied: Source IP is not whitelisted while on whitelist only key'
                );
                return false;
            }
        }

        if ($api_key->isFirewallBlackListed($ip)) {
            $response->code = 500;
            $response->setResponse('error', 'Access Denied: Source IP Blacklisted');
            return false;
        }

        return true;
    }
}
