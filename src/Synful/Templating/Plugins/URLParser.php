<?php

namespace Synful\Templating\Plugins;

use Spackle\Plugin;

class URLParser extends Plugin
{
    /**
     * The key notating the beginning of the element.
     *
     * @var string
     */
    public $key = 'url';

    /**
     * Parse the value for the element matching this plugin.
     *
     * @param string $data
     *
     * @return string
     */
    public function parse($data)
    {
        return sf_conf('system.domain').$data;
    }
}
