<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\Commands\Util\Command;
use Synful\Synful;

class Standalone extends Command
{
    /**
     * Construct the Standalone command.
     */
    public function __construct()
    {
        $this->name = 's';
        $this->description = 'Tells the system to open a local socket instead of relying on a web server.';
        $this->required = false;
        $this->alias = 'standalone';

        $this->exec = function () {
            Synful::$config->set('system.standalone', true);
            return true;
        };
    }
}