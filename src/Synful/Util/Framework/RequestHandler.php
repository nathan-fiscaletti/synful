<?php

namespace Synful\Util\Framework;

/**
 * Interface used to handle Request Handlers.
 */
class RequestHandler
{
    /**
     * Handles a POST request type.
     *
     * @param  Request $request
     * @return array|Response
     * @throws SynfulException
     */
    public function post(Request $request)
    {
        throw new SynfulException(500, 1021);
    }

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     * @throws \Synful\Util\Framework\SynfulException
     */
    public function get(Request $request)
    {
        throw new SynfulException(500, 1022);
    }

    /**
     * Handles a PUT request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     * @throws \Synful\Util\Framework\SynfulException
     */
    public function put(Request $request)
    {
        throw new SynfulException(500, 1023);
    }

    /**
     * Handles a DELETE request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     * @throws \Synful\Util\Framework\SynfulException
     */
    public function delete(Request $request)
    {
        throw new SynfulException(500, 1024);
    }

    /**
     * Function for handling request and returning a response.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     * @throws \Synful\Util\Framework\SynfulException
     */
    public function handleRequest(Request $request)
    {
        switch ($request->method) {
            case 'POST' : 
                return $this->post($request);
            case 'GET' :
                return $this->get($request);
            case 'PUT' :
                return $this->put($request);
            case 'DELETE' :
                return $this->delete($request);

            default :
                throw new SynfulException(500, 1025);
        }
    }
}
