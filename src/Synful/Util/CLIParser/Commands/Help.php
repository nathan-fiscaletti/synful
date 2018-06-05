<?php

namespace Synful\Util\CLIParser\Commands;

use Synful\Util\CLIParser\CommandLine;
use Synful\Util\CLIParser\Commands\Util\Command;

class Help extends Command
{
    /**
     * Construct the Help command.
     */
    public function __construct()
    {
        $this->name = 'h';
        $this->description = 'Displays usage and descriptions for all comamnd line parameters.';
        $this->required = false;
        $this->alias = 'help';
        $this->exec = function () {
            $cl = new CommandLine();
            $cl->loadParameters();
            $cl->printUsage();

            return parameter_result_halt();
        };
    }
}
