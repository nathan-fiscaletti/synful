<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Synful;
use Synful\Util\CLIParser\Commands\Util\Command;

class Version extends Command
{
    /**
     * Construct the Version command.
     */
    public function __construct()
    {
        $this->name = 'v';
        $this->description = 'Check the current version of the framework.';
        $this->required = false;
        $this->alias = 'version';

        $this->exec = function () {
            sf_info('Synful '.Synful::version());

            return parameter_result_halt();
        };
    }
}
