<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Synful;

class Color extends Command
{
    /**
     * Construct the Color command.
     */
    public function __construct()
    {
        $this->name = 'cl';
        $this->description = 'Use to enable/disable console color at run time.';
        $this->required = false;
        $this->alias = 'color';

        $this->exec = function ($bool) {
            Synful::$config->set('system.color', $bool);
            return ($bool) ? 1 : 0;
        };
    }
}