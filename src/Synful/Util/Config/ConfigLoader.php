<?php

namespace Synful\Util\Config;

use DirectoryIterator;
use Synful\Util\Framework\Object;
use Gestalt\Loaders\LoaderInterface;

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

        return $items;
    }
}
