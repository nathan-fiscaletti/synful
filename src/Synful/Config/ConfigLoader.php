<?php

namespace Synful\Config;

use Synful\Framework\ParamObject;

use Gestalt\Loaders\JsonDirectoryLoader;
use Gestalt\Loaders\YamlDirectoryLoader;
use Gestalt\Loaders\PhpDirectoryLoader;
use Gestalt\Loaders\IniDirectoryLoader;

class ConfigLoader
{
    use ParamObject;

    /**
     * The directory to load PHP configuration files from.
     *
     * @var array
     */
    protected $directory;

    /**
     * Load the configuration items and return them as an array.
     *
     * @return array
     */
    public function load()
    {
        $results = [];

        if (function_exists('yaml_parse_file')) {
            $result = (new YamlDirectoryLoader($this->directory))->load();
        }

        $result = array_merge($result, (new JsonDirectoryLoader($this->directory))->load());
        $result = array_merge($result, (new IniDirectoryLoader($this->directory))->load());
        $result = array_merge($result, (new PhpDirectoryLoader($this->directory))->load()); 

        return $result;
    }
}
