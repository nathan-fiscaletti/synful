<?php

namespace Synful\Framework;

use Exception;

class SynfulException extends Exception
{
    /**
     * The error code.
     *
     * @var int
     */
    private $error;

    /**
     * The error message.
     *
     * @var string
     */
    protected $message = null;

    /**
     * The Response Object.
     *
     * @var \Synful\Response
     */
    public $response;

    /**
     * Error message definitions.
     *
     * @var array
     */
    private $error_messages = [
        1001 => 'Unknown endpoint.',
        1002 => 'Either you do not have mod-rewrite enabled or there is no request handler defined in your request.',
        1003 => 'Bad Request: Insufficient security level.',
        1006 => 'Bad Request: API Authentication failed.',
        1007 => 'Bad Request: Key has been disabled.',
        1008 => 'Bad Request: Key not whitelisted for specified handler.',
        1009 => 'Bad Request: No key defined.',
        1010 => 'Bad Request: No user defined.',
        1011 => 'Access Denied: Source IP is not whitelisted while on whitelist only key.',
        1012 => 'Access Denied: Source IP Blacklisted.',
        1013 => 'Bad Request.',
        1015 => 'Unable to connect to MySql server. Check \'SqlServers.php\'.',
        1016 => 'Invalid response type returned from Request Handler.',
        1017 => 'Invalid middleware definition.',
        1018 => 'Invalid field count in request path.',
        1019 => 'Invalid path element.',
        1020 => 'Invalid input data.',
        1021 => 'POST handler not defined in selected RequestHandler.',
        1022 => 'GET handler not defined in selected RequestHandler.',
        1023 => 'PUT handler not defined in selected RequestHandler.',
        1024 => 'DELETE handler not defined in selected RequestHandler.',
        1025 => 'OPTIONS handler not defined in selected RequestHandler.',
        1026 => 'PATCH handler not defined in selected RequestHandler.',
        1027 => 'Invalid request type. Supported request types: POST, GET, PUT, DELETE.',
        1028 => 'Global rate limit exceeded.',
        1031 => 'Attempting to load APCu for RateLimit but php-apcu extension not installed.',
        1032 => 'Endpoint is not allowed for this API Key',
        1033 => 'Valid methods for the RateLimit Middleware are \'ip\' and \'api_key\'.',
        1034 => 'When configured with the \'api_key\' method, the RateLimit Middleware requires that the APIKeyValidation middleware also be applied to the route.',
        1035 => 'Missing properties for the RateLimit Middleware',
        1036 => 'Rate limit exceeded.',
    ];

    /**
     * Construct the SynfulException object.
     *
     * @param int              $code
     * @param int              $error
     * @param string           $message
     */
    public function __construct($code, $error, $message = null)
    {
        $this->message = ($message == null) ? $this->getErrorMessage($error) : $message;
        parent::__construct($this->message, $error);
        $this->error = $error;
        $this->response = sf_response(
            $code,
            [
                'error_code' => $error,
                'error' => $this->message,
            ]
        );
    }

    /**
     * Retrieve error message based on error code.
     *
     * @param  int    $error
     * @return string
     */
    public function getErrorMessage($error = null)
    {
        if ($error == null) {
            $error = $this->error;
        }

        $ret = null;

        if ($this->message != null) {
            $ret = $this->message;
        } else {
            if (in_array($error, array_keys($this->error_messages))) {
                $ret = $this->error_messages[$error];
            } else {
                $ret = $this->error_messages[1013];
            }
        }

        return $ret;
    }
}
