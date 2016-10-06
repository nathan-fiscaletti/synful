<?php

namespace Synful\Util\Config;

use Gestalt\Loaders\LoaderInterface;
use Synful\Util\Framework\Object;
use DirectoryIterator;

class ConfigLoader implements LoaderInterface
{
    use Object;

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
        $items = [];
        $directory = new DirectoryIterator(realpath($this->directory));

        foreach ($directory as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {
                $filename = $file->getFilename();
                $config = strtolower(substr($filename, 0, strrpos($filename, '.')));
                $items[$config] = require $file->getPathname();
            }
        }

        foreach ($items['sqlservers'] as $server_name => $server) {
            foreach ($server['databases'] as $database_name => $database) {
                if (isset($database['use'])) {
                    if (array_key_exists($database['use'], $server['databases'])) {
                        $database = array_merge($server['databases'][$database['use']], $database);
                        unset($database['use']);
                        $items['sqlservers'][$server_name]['databases'][$database_name] = $database;
                    }
                }
            }
        }

        return $items;
    }
}
