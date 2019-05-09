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
            $results = (new YamlDirectoryLoader($this->directory))->load();
        } else {
            throw new \Exception('Synful requires the yaml extension to run.');
        }

        $results = array_merge($results, (new JsonDirectoryLoader($this->directory))->load());
        $results = array_merge($results, (new IniDirectoryLoader($this->directory))->load());
        $results = array_merge($results, (new PhpDirectoryLoader($this->directory))->load()); 

        return $results;
    }
}
