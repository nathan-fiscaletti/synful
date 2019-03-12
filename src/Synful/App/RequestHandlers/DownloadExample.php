<?php

namespace Synful\App\RequestHandlers;

use Synful\Util\Framework\Request;
use Synful\Util\Framework\RequestHandler;

/**
 * Class used to demonstrate using a Response as a Download.
 */
class DownloadExample extends RequestHandler
{
    /**
     * Override the handler endpoint
     * Example: http://myapi.net/user/search
     * uses the endpoint `user/search`.
     *
     * @var string
     */
    public $endpoint = 'example/download';

    /**
     * Handles a GET request type.
     *
     * @param  \Synful\Util\Framework\Request $request
     * @return \Synful\Util\Framework\Response|array
     */
    public function get(Request $request)
    {
        return sf_response(
            200,
            [
                'data' => 'This is the content of the downloaded file.',
            ]
        )->downloadableAs('text.txt');
    }
}
