<?php

namespace Synful\Templating;

use Exception;
use Spackle\FileParser;

class Template extends FileParser
{
    /**
     * Construct the Template using a Template Name.
     *
     * @param string $name          The name of the template to load.
     * @param array  $substitutions Optional substitutions.
     * @param bool   $system        If true, the template will be loaded
     *                              from the "Synful" directory instead
     *                              of the "App" directory.
     *
     * @throws Exception
     */
    public function __construct($name, $substitutions = [], $system = false)
    {
        parent::__construct(
            './templates/'.($system?'Synful':'App').'/'.$name,
            $substitutions
        );
    }
}
