<?php

namespace Synful\Templating\Plugins;

use Spackle\Plugin;

class ConfigParser extends Plugin
{
    /**
     * The key notating the beginning of the element.
     *
     * @var string
     */
    public $key = 'conf';

    /**
     * Parse the value for the element matching this plugin.
     *
     * @param string $data
     *
     * @return string
     */
    public function parse($data)
    {
        return sf_conf($data);
    }
}
