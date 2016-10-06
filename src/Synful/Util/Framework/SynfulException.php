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
            switch ($error) {
                case 1001: {
                    $ret = 'Unknown handler. Handlers are case sensitive.';
                    break;
                }

                case 1002: {
                    $ret = 'No handler defined.';
                    break;
                }

                case 1003: {
                    $ret = 'Bad request: Invalid request field supplied. Not array.';
                    break;
                }

                case 1004: {
                    $ret = 'Bad Request: Invalid request field supplied. Not Object.';
                    break;
                }

                case 1005: {
                    $ret = 'Bad Request: Missing request field.';
                    break;
                }

                case 1006: {
                    $ret = 'Bad Request: Invalid user or key.';
                    break;
                }

                case 1007: {
                    $ret = 'Bad Request: Key has been disabled.';
                    break;
                }

                case 1008: {
                    $ret = 'Bad Request: Key not whitelisted for specified handler.';
                    break;
                }

                case 1009: {
                    $ret = 'Bad Request: No key defined.';
                    break;
                }

                case 1010: {
                    $ret = 'Bad Request: No user defined.';
                    break;
                }

                case 1011: {
                    $ret = 'Access Denied: Source IP is not whitelisted while on whitelist only key.';
                    break;
                }

                case 1012: {
                    $ret = 'Access Denied: Source IP Blacklisted.';
                    break;
                }

                case 1013: {
                    $ret = 'Bad Request.';
                    break;
                }

                default: {
                    $ret = 'Unknown Error';
                }
            }
        }

        return $ret;
    }
}
