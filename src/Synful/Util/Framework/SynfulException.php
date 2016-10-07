<?php

namespace Synful\Util\Framework;

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
        1001 => 'Unknown handler. Handlers are case sensitive.',
        1002 => 'No handler defined.',
        1003 => 'Bad request: Invalid request field supplied. Not array.',
        1004 => 'Bad Request: Invalid request field supplied. Not Object.',
        1005 => 'Bad Request: Missing request field.',
        1006 => 'Bad Request: Invalid user or key.',
        1007 => 'Bad Request: Key has been disabled.',
        1008 => 'Bad Request: Key not whitelisted for specified handler.',
        1009 => 'Bad Request: No key defined.',
        1010 => 'Bad Request: No user defined.',
        1011 => 'Access Denied: Source IP is not whitelisted while on whitelist only key.',
        1012 => 'Access Denied: Source IP Blacklisted.',
        1013 => 'Bad Request.',
    ];

    /**
     * Construct the SynfulException object.
     *
     * @param \Synful\Response $response
     * @param int              $code
     * @param int              $error
     * @param string           $message
     */
    public function __construct($response, $code, $error, $message = '')
    {
        $this->message = ($message == null) ? $this->getErrorMessage($error) : $message;
        parent::__construct($this->message, $error);
        $this->error = $error;
        $this->response = ($response == null) ? new Response : $response;
        $this->response->code = $code;
        $this->response->overloadResponse([
            'error_code' => $error,
            'error' => $this->message,
        ]);
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
